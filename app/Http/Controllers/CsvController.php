<?php

namespace App\Http\Controllers;

use App\Traits\ShiptheoryAPI;
use Illuminate\Http\Request;
use Redirect;
use Session;

class CsvController extends Controller
{
    // Traits contain reuseable functions this controller can access via $this->
    use ShiptheoryAPI; // (Shiptheory API) Trait for consuming external API

    private $column_values = [
            "sku" => "sku",
            "name" => "name",
            "price" => "price",
            "weight" => "weight",
            "barcode" => "barcode",
            "commodity_code" => "commodity_code",
            "commodity_description" => "commodity_description",
            "commodity_manucountry" => "commodity_manucountry",
            "commodity_composition" => "commodity_composition",
            "length" => "length",
            "width" => "width",
            "height" => "height"
        ];

    public function welcome(Request $request){
        $viewable['mode'] = Session::get('mode', 'import');

        $token = $this->getToken();
        if(empty($token['error'])) $viewable['authed'] = true;

        return view('welcome', $viewable);
    }

    private function login(Request $request)
    {
        $request->validate([
            'email' => 'required|max:1000',
            'password' => 'required|max:1000'
        ]);

        $checkCredentials = $this->check_shiptheory_credentials(
            $request->email,
            $request->password);
        if(!empty($checkCredentials['error'])) return $checkCredentials;

        $token = $this->getToken($request->email, $request->password);
        if(!empty($token['error'])) return $token;

        return ['success' => true];
    }

    public function logout(Request $request)
    {
        Session::flush();
        return Redirect::to('/')->with(['success' => 'Logged out']);
    }

    public function import_csv(Request $request){
        $this->clear_flash_message();
        $redirect = '/';

        // Set mode to import or update
        if(empty($request->mode)){
            session()->flash('error', 'Mode required');
            return Redirect::to($redirect);
        }else{
            if($request->mode == 'import' || $request->mode == 'update'){
                $viewable['mode'] = $request->mode;
                Session::put('mode', $viewable['mode']);
            }else{
                session()->flash('error', 'Invalid mode');
                return Redirect::to($redirect);
            }
        }

        // Check if we are authenticated
        $token = $this->getToken();
        if(!empty($token['error'])){
            // Try to authenticate
            $login = $this->login($request);
            if(!empty($login['error'])){
                return Redirect::to($redirect)->with($login);
            }
        }
        $viewable['authed'] = true;

        // Check valid file type uploaded
        $validatedData = $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048'
        ]);
        $csv = $request->file('csv_file');

        // Read rows from the file
        $file_handle = fopen($csv, 'r');
        $rows = [];
        while (!feof($file_handle)) {
            $rows[] = fgetcsv($file_handle, 0, ',' /*delimiter*/);
        }
        fclose($file_handle);

        // Define which values are in which columns
        $headingRow = 0;
        $column_value_map = [];
        foreach($rows[$headingRow] as $key => $val){
            if(!empty($this->column_values[strtolower($val)])){
                $column_value_map[$key] = $this->column_values[strtolower($val)];
            }
        }

        // Validate there is a product sku column as that is required by Shiptheory
        if(!in_array('sku', $column_value_map)){
            return Redirect::to($redirect)->with(
                ['error' => 'Could not find product SKU column. Ensure that the first row of your CSV file has the <strong>field titles</strong> like sku, name, price etc.']);
        }

        // Add products to Shiptheory
        $fails = 0; $successes = 0;
        foreach($rows as $key => $row){
            if($key == $headingRow) continue; // Skip heading row

            // Organise the values from the row with the column names
            $product_params = [];
            foreach($column_value_map as $mapkey => $col_name){
                $product_params[$col_name] = $row[$mapkey];
            }

            // Validate row
            if(empty($product_params['sku'])){
                // Note failed attempt and try next row
                $fails++;
                $viewable['report'][$key] = [
                    'error' => true,
                    'info' => "No value in sku column found for this row."];
                continue;
            }

            if($viewable['mode'] == 'import'){
                // Add product to shiptheory
                $response = $this->callShiptheory('POST','products', [], $product_params);
                if(!empty($response['error'])){
                    // Note failed attempt and try next row
                    $fails++;
                    $viewable['report'][$key] = $this->reportError(
                        $response['error'], $product_params);
                    continue;
                }
                $response = json_decode($response);
            }else{
                // Update product in shiptheory
                $response = $this->callShiptheory('PUT',
                    'products/update/'.$product_params['sku'], [], $product_params);
                if(!empty($response['error'])){
                    // Note failed attempt and try next row
                    $fails++;
                    $viewable['report'][$key] = $this->reportError(
                        $response['error'], $product_params);
                    continue;
                }
                $response = json_decode($response);
            }
            if(!empty($response->error)){
                $fails++;
                $viewable['report'][$key] = $this->reportError(
                    $response->error, $product_params);
            }else{
                $successes++;
            }
        }

        $reply_type = 'success';
        if($successes == 0) $reply_type = 'error';
        if($successes > 0 && $fails > 0) $reply_type = 'warning';
        $action = ($viewable['mode'] == 'import') ? 'added' : 'updated';
        $viewable[$reply_type] = $successes. " products ".$action ." successfully and ".
            $fails." rows in the csv file failed.";
        session()->flash($reply_type, $viewable[$reply_type]);
        return view('welcome', $viewable);
    }

    private function reportError($errorMsg, $product_params){
        $info = '';
        if(!empty($product_params['sku'])) $info .= $product_params['sku'].' ';
        if(!empty($product_params['name'])) $info .= $product_params['name'].' ';
        $info .= 'failed.';
        return [
            'error' => $errorMsg,
            'info' => $info];
    }

    public function export_csv(Request $request){
        $this->clear_flash_message();
        $viewable['mode'] = 'export';
        $redirect = '/';

        // Check if we are authenticated
        $token = $this->getToken();
        if(!empty($token['error'])){
            // Try to authenticate
            $login = $this->login($request);
            if(!empty($login['error'])){
                return Redirect::to($redirect)->with($login);
            }
        }
        $viewable['authed'] = true;

        // Get products from Shiptheory
        $results = [];
        $method = 'GET';
        $endpoint = 'products';
        $queryParams = ['limit' => 100];
        $response = $this->callShiptheory($method, $endpoint, $queryParams);
        $response = json_decode($response);
        $results = array_merge($results, $response->products);

        // Get the next page of results if there is one
        $pages = $response->pagination->pages;
        $page = 2;
        while($page <= $pages){
            $queryParams['page'] = $page;
            $response = $this->callShiptheory($method, $endpoint, $queryParams);
            $response = json_decode($response);
            $results = array_merge($results, $response->products);
            $page++;
        }

        // Create the CSV file
        $fileName = 'shiptheory_products.csv';
        $columns = $this->column_values;
        $callback = function() use($results, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($results as $row) {
                fputcsv($file, (array)$row);
            }
            fclose($file);
        };

        // Serve up the CSV file for download
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );
        return response()->stream($callback, 200, $headers);
    }

    function clear_flash_message(){
        Session::put('error', null);
        Session::put('success', null);
        Session::put('warning', null);
        Session::put('info', null);
    }


}
