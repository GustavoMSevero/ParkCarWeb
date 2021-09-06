SELECT p.`licensePlate`, p.`entrance`, p.`vehicleParkStatus`,  v.`model`, v.`brand`, pa.`parkingName`
FROM  parkedVehicles p, `client` c, clientVehicle v, parking pa
WHERE c.`idClient` = p.`idClient`
AND c.`idClient` = v.`idClient`
AND p.`vehicleParkStatus`=1
AND c.`idClient` = 29
AND pa.`idParking` = p.`idParking`;




WHERE c.idClient=29
AND pa.`idParking`=21
AND v.`idClient`=c.idClient
AND p.`vehicleParkStatus`=1