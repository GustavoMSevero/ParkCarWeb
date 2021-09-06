<?PHP
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once('con.php');

$pdo = conectar();


function sendMessage($msg, $users) {
    $content = array(
        "en" => $msg
    );
    $hashes_array = array();
    $fields = array(
        "app_id" => "6cf36d3e-d61b-4ce9-8963-3e31fd3222df",
        "include_player_ids" => $users,
        "data" => array(
            "foo" => "bar"
        ),
        "contents" => $content,
        'web_buttons' => $hashes_array
    );

    $fields = json_encode($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic OWY3NDM3NzMtZTIzOS00YzhlLTlkNzItMzc4YTQ0OGZlMTE1'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return $response;
}


function updateOneSignalId($userId, $userType, $oneSignalId) {
    $pdo = conectar();
    $searchOneSingalId=$pdo->prepare("SELECT id FROM deviceIds 
                                    WHERE id=:oneSignalId AND userType=:userType");
    $searchOneSingalId->bindValue(":oneSignalId", $oneSignalId);
    $searchOneSingalId->bindValue(":userType", $userType);
    $searchOneSingalId->execute();

    $exists = $searchOneSingalId->rowCount();

    if($exists === 0) {
        $storeDeviceId=$pdo->prepare("INSERT INTO deviceIds (id, userId, userType) VALUES(?,?,?)");
        $storeDeviceId->bindValue(1, $oneSignalId);
        $storeDeviceId->bindValue(2, $userId);
        $storeDeviceId->bindValue(3, $userType);

        $storeDeviceId->execute();
    } else {
        $updateDeviceId = $pdo->prepare("UPDATE deviceIds SET id=:oneSignalId 
                                    WHERE userId=:userId AND userType=:userType");
        $updateDeviceId->bindValue(":userId", $userId);
        $updateDeviceId->bindValue(":oneSignalId", $oneSignalId);
        $updateDeviceId->bindValue(":userType", $userType);

        $updateDeviceId->execute();
    }
}

function checkIfUserIdExists($userId, $userType, $oneSignalId) {
    $pdo = conectar();
    $searchUserId=$pdo->prepare("SELECT userId FROM deviceIds 
                                WHERE userId=:idClient AND userType=:userType");
    $searchUserId->bindValue(":idClient", $idClient);
    $searchUserId->bindValue(":userType", $userType);
    $searchUserId->execute();

    $exists = $searchUserId->rowCount();

    if($exists === 0) {
        $storeUserId=$pdo->prepare("INSERT INTO deviceIds (id, userId, userType) VALUES(?,?,?)");
        $storeUserId->bindValue(1, $oneSignalId);
        $storeUserId->bindValue(2, $userId);
        $storeUserId->bindValue(3, $userType);

        $storeUserId->execute();
    } else {
        $updateUserId=$pdo->prepare("UPDATE deviceIds SET userId=:userId 
                                    WHERE id=:oneSignalId AND userType=:userType");
        $updateUserId->bindValue(":userId", $userId);
        $updateUserId->bindValue(":oneSignalId", $oneSignalId);
        $updateUserId->bindValue(":userType", $userType);

        $updateUserId->execute();
    }

}
?>