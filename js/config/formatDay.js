
export function formatDay(horario) {

    var diaP = horario.data.toString().split(" ");

        if(diaP[1] == 'Jan'){
            diaP[1] = 1;
        }
        if(diaP[1] == 'Feb'){
            diaP[1] = 2;
        }
        if(diaP[1] == 'Mar'){
            diaP[1] = 3;
        }
        if(diaP[1] == 'Apr'){
           diaP[1] = 4;
        }
        if(diaP[1] == 'May'){
            diaP[1] = 5;
        }
        if(diaP[1] == 'Jun'){
            diaP[1] = 6;
        }
        if(diaP[1] == 'Jul'){
            diaP[1] = 7;
        }
        if(diaP[1] == 'Aug'){
            diaP[1] = 8;
        }
        if(diaP[1] == 'Sep'){
            diaP[1] = 9;
        }
        if(diaP[1] == 'Oct'){
            diaP[1] = 10;
        }
        if(diaP[1] == 'Nov'){
            diaP[1] = 11;
        }
        if(diaP[1] == 'Dec'){
            diaP[1] = 12;
        }
         
        //Monta dia
        var dia = diaP[2]+'/'+diaP[1]+'/'+diaP[3];

        return dia;
}