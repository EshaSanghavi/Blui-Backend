<?php

namespace App\Services;

use App\Models\IthinkLogistics;

class IthinkLogisticsService

{

    public function getState()
    {
        $ithink_logistics = IthinkLogistics::first();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL             => "https://pre-alpha.ithinklogistics.com/api_v3/state/get.json",
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => "POST",
            CURLOPT_POSTFIELDS      => "{\"data\":{\"country_id\":\"101\",\"access_token\":\"$ithink_logistics->access_token\",\"secret_key\":\"$ithink_logistics->secret_key\"}}\n",
            CURLOPT_HTTPHEADER      => array(
                "cache-control: no-cache",
                "content-type: application/json"
            )
        ));

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);
        if ($err) 
        {
            // echo "cURL Error #:" . $err;
            return response()->json($err);
        }
        else
        {
            // echo $response;
            return response()->json($response);
        }  
    }

    public function getCity($state_id)
    {
        $ithink_logistics = IthinkLogistics::first();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL             => "https://pre-alpha.ithinklogistics.com/api_v3/city/get.json",
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => "POST",
            CURLOPT_POSTFIELDS      => "{\"data\":{\"state_id\":\"$state_id\",\"access_token\":\"$ithink_logistics->access_token\",\"secret_key\":\"$ithink_logistics->secret_key\"}}\n",
            CURLOPT_HTTPHEADER      => array(
                "cache-control: no-cache",
                "content-type: application/json"
            )
        ));

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);
        if ($err) 
        {
            // echo "cURL Error #:" . $err;
            return response()->json($err);
        }
        else
        {
            // echo $response;
            return response()->json($response);
        } 
    }

    public function addWarehouse($data)
    {
        $company_name = $data['company_name'];
        $mobile = $data['mobile'];
        $address1 = $data['address1'];
        $address2 = $data['address2'];
        $state_id = $data['state_id'];
        $city_id = $data['city_id'];
        $pincode = $data['pincode'];
        $country_id = 101;
        $gps = $data['gps'];
    
        $ithink_logistics = IthinkLogistics::first();        

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL             => "https://pre-alpha.ithinklogistics.com/api_v3/warehouse/add.json",
          CURLOPT_RETURNTRANSFER  => true,
          CURLOPT_ENCODING        => "",
          CURLOPT_MAXREDIRS       => 10,
          CURLOPT_TIMEOUT         => 30,
          CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST   => "POST",
          CURLOPT_POSTFIELDS      => "{\"data\":{\"company_name\":\"$company_name\",\"address1\":\"$address1\",\"address2\":\"$address2\",\"mobile\":\"$mobile\",\"pincode\":\"$pincode\",\"city_id\":\"$city_id\",\"state_id\":\"$state_id\",\"country_id\":\"101\",\"gps\":\"$gps\",\"access_token\":\"$ithink_logistics->access_token\",\"secret_key\":\"$ithink_logistics->secret_key\"}}\n",
          CURLOPT_HTTPHEADER      => array(
              "cache-control: no-cache",
              "content-type: application/json"
          )
        ));
    
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        if ($err) 
        {
            // echo "cURL Error #:" . $err;
            return response()->json($err);
        }
        else
        {
            // echo $response;
            return response()->json($response);
        }
    }

    public function orderAdd()
    {
        $ithink_logistics = IthinkLogistics::first();        

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL             => "https://pre-alpha.ithinklogistics.com/api_v3/order/add.json",
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_ENCODING        => "",
        CURLOPT_MAXREDIRS       => 10,
        CURLOPT_TIMEOUT         => 30,
        CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST   => "POST",
        CURLOPT_POSTFIELDS      => "{\"data\":{\"shipments\":[{\"waybill\":\"\",\"order\":\"GK0034\",\"sub_order\":\"\",\"order_date\":\"31-01-2018\",\"total_amount\":\"999\",\"name\":\"Bharat\",\"company_name\":\"ABC Company\",\"add\":\"104 Shreeji\",\"add2\":\"\",\"add3\":\"\",\"pin\":\"400067\",\"city\":\"Mumbai\",\"state\":\"Maharashtra\",\"country\":\"India\",\"phone\":\"9876543210\",\"alt_phone\":\"9876542210\",\"email\":\"abc@gmail.com\",\"is_billing_same_as_shipping\":\"no\",\"billing_name\":\"Bharat\",\"billing_company_name\":\"ABC Company\",\"billing_add\":\"104, Shreeji Sharan\",\"billing_add2\":\"\",\"billing_add3\":\"\",\"billing_pin\":\"400067\",\"billing_city\":\"Mumbai\",\"billing_state\":\"Maharashtra\",\"billing_country\":\"India\",\"billing_phone\":\"9876543210\",\"billing_alt_phone\":\"9876543211\",\"billing_email\":\"abc@gmail.com\",\"products\":[{\"product_name\":\"Green color tshirt\",\"product_sku\":\"GC001-1\",\"product_quantity\":\"1\",\"product_price\":\"100\",\"product_tax_rate\":\"5\",\"product_hsn_code\":\"91308\",\"product_discount\":\"0\"},{\"product_name\":\"Red color tshirt\",\"product_sku\":\"GC002-2\",\"product_quantity\":\"1\",\"product_price\":\"200\",\"product_tax_rate\":\"5\",\"product_hsn_code\":\"91308\",\"product_discount\":\"0\"}],\"shipment_length\":\"10\",\"shipment_width\":\"10\",\"shipment_height\":\"5\",\"weight\":\"400.00\",\"shipping_charges\":\"0\",\"giftwrap_charges\":\"0\",\"transaction_charges\":\"0\",\"total_discount\":\"0\",\"first_attemp_discount\":\"0\",\"cod_amount\":\"550\",\"payment_mode\":\"COD\",\"reseller_name\":\"\",\"eway_bill_number\":\"\",\"gst_number\":\"\",\"what3words\":\"\",\"return_address_id\":\"24\"}],\"pickup_address_id\":\"$pickup_address_id\",\"access_token\":\"$ithink_logistics->access_token\",\"secret_key\":\"$ithink_logistics->secret_key\",\"logistics\":\"$logistics\",\"s_type\":\"$s_type\",\"order_type\":\"$order_type\"}}",
        CURLOPT_HTTPHEADER      => array(
            "cache-control: no-cache",
            "content-type: application/json"
        )
        ));

        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);
        if ($err) 
        {
            // echo "cURL Error #:" . $err;
            return response()->json($err);
        }
        else
        {
            // echo $response;
            return response()->json($response);
        }   
    }

}