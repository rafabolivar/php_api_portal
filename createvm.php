<html>

<body>

<?php

$clusteruuid = "0005bb83-32dd-16c0-0000-000000013c2e";
$prodnetuuid = "60433aca-5a69-4ebc-a995-33687561f292";
$devnetuuid = "1aa68e13-8f38-4fc9-bc89-8e1cc1c2d660";
$diskimg = "9cada634-4cf8-4ffe-a827-1ccd60a53565";
$cpu = 4;
$ram = 4096;
$nic = "0419cb14-deee-4b59-8435-714d63f45220";
$nicprefix ="0419cb14-deee-4b59-8435-";
$vmname = $_REQUEST['vmname'];
$cluster = $_REQUEST['cluster'];
$network = $_REQUEST['network'];
$vmsize =$_REQUEST['vmsize'];

$curl = curl_init();

if ($cluster == "clusterone"){
  $cluster = $clusteruuid;
}

switch ($vmsize) {
    case "small":
  $cpu = 2;
  $ram = 2048;
        break;
    case "medium":
  $cpu = 4;
        $ram = 4096;
        break;
    case "large":
  $cpu = 8;
        $ram = 8192;
        break;
}

switch ($network) {
    case "prodnetwork":
        $network = $prodnetuuid;
        break;
    case "devnetwork":
        $network = $devnetuuid;
        break;
}

$hexsufix = bin2hex(random_bytes(6));
$nic = $nicprefix . $hexsufix;

/*
echo "$cluster \n";
echo "$vmsize \n";
echo "$cpu \n";
echo "$ram \n";
echo "$network \n";
echo "$hexsufix \n";
echo "$nic \n";
 */

curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt_array($curl, array(
  CURLOPT_PORT => "9440",
  CURLOPT_URL => "https://10.42.104.39:9440/api/nutanix/v3/vms",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",

  CURLOPT_POSTFIELDS => "{\"metadata\":{\"kind\":\"vm\"},\"spec\":{\"name\":\"$vmname\",\"cluster_reference\":{\"kind\":\"cluster\",\"uuid\":\"$cluster\"},\"resources\":{\"disk_list\":[{\"data_source_reference\":{\"kind\":\"image\",\"uuid\":\"$diskimg\"},\"device_properties\":{\"device_type\":\"DISK\",\"disk_address\":{\"adapter_type\": \"SCSI\",\"device_index\":0}}}],\"nic_list\":[{\"subnet_reference\":{\"uuid\":\"$network\",\"kind\":\"subnet\"},\"ip_endpoint_list\":[],\"is_connected\":true,\"uuid\":\"$nic\"}],\"memory_size_mib\":$ram,\"num_sockets\":$cpu,\"num_vcpus_per_socket\":1,\"power_state\":\"ON\"}}}",

  CURLOPT_HTTPHEADER => array(
    "authorization: Basic YWRtaW46bngyVGVjaDEyMyE=",
    "content-type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
//  echo $response;
readfile('index.html');
}

?>

</body>
</html>
