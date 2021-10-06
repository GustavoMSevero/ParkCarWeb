<?php
    require_once('../con.php');

    $pdo = conectar();

    $latMax = -30.0158769;
    $latMin = -30.0338769;
    $lngMax = -51.1745442;
    $lngMin = -51.1925442;

    $latMax = floatval($latMax);
    $latMin = floatval($latMin);
    $lngMax = floatval($lngMax);
    $lngMin = floatval($lngMin);

    try {
        $searchForNearbyParking=$pdo->prepare("SELECT parkingName, address, addressNumber, vaccantNumber
                                        FROM parking
                                        WHERE lat BETWEEN :latMin AND :latMax 
                                        AND lng BETWEEN :lngMin AND :lngMax 
                                        AND activate=1");
        $searchForNearbyParking->bindvalue(":latMin", $latMin);
        $searchForNearbyParking->bindvalue(":latMax", $latMax);
        $searchForNearbyParking->bindvalue(":lngMin", $lngMin);
        $searchForNearbyParking->bindvalue(":lngMax", $lngMax);
        $searchForNearbyParking->execute();

        // $searchForNearbyParking->debugDumpParams();

        $quantity = $searchForNearbyParking->rowCount();

        if ($quantity != 0) {
            
            while ($line=$searchForNearbyParking->fetch(PDO::FETCH_ASSOC)) {

                $parkingName = $line['parkingName'];
                $address = $line['address'];
                $addressNumber = $line['addressNumber'];
                $vaccantNumber = $line['vaccantNumber'];
                

                $return[] = array(
                    'parkingName' => $parkingName,
                    'address' => $address,
                    'addressNumber' => $addressNumber,
                    'vaccantNumber' => $vaccantNumber
                );
    
            }

            echo json_encode($return);
        
        } else {

            $status = 0;
            $msg = 'Nenhum estacionamento próximo do seu destino';
            $return = array(
                'status' => $status,
                'msg' => $msg
            );

            echo json_encode($return);
            
        }

        
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

?>