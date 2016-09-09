<?php

class HubspotSync
{
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function createContactAndDeals($address, $customer, $products, $state, $transactionDate = null)
    {
        // because here can be different API's, so we do not know how named "date" field in exact API, so we use current UNIX timestamp instead
        // $transactionDate = strtotime($transaction["payment_date"]) * 1000;
        if (!$transactionDate) {
            $transactionDate = round(microtime(true) * 1000); // timestamp with milliseconds
        }

        $contact = $this->createContact(
            $customer->email,
            array(
                'firstname' => $customer->firstname,
                'lastname' => $customer->lastname,
                'website' => $customer->website,
                'company' => $address->company,
                'phone' => ($address->phone ? $address->phone : $address->phone_mobile),
                'address' => $address->address1 . ' ' . $address->address2,
                'city' => $address->city,
                'state' => $state,
                'zip' => $address->postcode,
                'lifecyclestage' => 'customer',
                'closedate' => $transactionDate
            ));

        foreach ($products as $product) {
            $deals[] = $this->createDeal($contact->vid, $product["id_product"], $product["name"], $product["total_wt"], $transactionDate);
        }
//        file_put_contents(dirname(__FILE__) . '/log/contact-deals-'.time() .'.log', var_export($contact,true) . var_export($deals,true));
    }

    private function createContact($email, $params)
    {
        $endpoint = 'http://api.hubapi.com/contacts/v1/contact/createOrUpdate/email/' . rawurlencode($email);
        $properties = array();
        foreach ($params as $key => $value) {
            if (isset($value) && $value)
                array_push($properties, array("property" => $key, "value" => $value));
        }
        $body = json_encode(array("properties" => $properties));
        return json_decode($this->request($endpoint, $body));
    }

    private function request($url, $body)
    {
        $url = $url . '?hapikey=' . $this->apiKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $output = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        $status = (isset($info['http_code'])) ? $info['http_code'] : 0;
        curl_close($ch);
        if ($errno > 0) {
            throw new Exception($error);
        } else if ($status < 200 || $status >= 300) {
            throw new Exception($output);
        } else {
            return $output;
        }
    }

    private function createDeal($contactVid, $dealId, $dealName, $dealPrice, $dealDate)
    {
        $endpoint = 'http://api.hubapi.com/deals/v1/deal';
        $data = array(
            "associations" => array(
                "associatedVids" => array($contactVid)
            ),
            "properties" => array(
                array("name" => "deal_id", "value" => $dealId),
                array("name" => "dealname", "value" => $dealName),
                array("name" => "amount", "value" => $dealPrice),
                array("name" => "closedate", "value" => $dealDate),
                array("name" => "dealstage", "value" => "closedwon")
            )
        );
        return json_decode($this->request($endpoint, json_encode($data)));
    }
}

