<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Workload;
use App\Models\SaglikSertifika;

class VeterinerEvrakDurumularıKontrolu
{


    public function vet_evrak_durum_kontrol($id){

        $veteriner = User::with('evraks.evrak.evrak_durumu')->find($id);
        $isi_var_mi = false;

        if($veteriner->evraks){
            $isi_var_mi = $veteriner->evraks->contains(fn($data) => $data->evrak->evrak_durumu->evrak_durum === 'İşlemde');
        }else{
            return $isi_var_mi;
        }

        return $isi_var_mi;

    }

}
