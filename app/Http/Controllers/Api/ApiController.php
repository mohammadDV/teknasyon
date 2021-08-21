<?php
namespace App\Http\Controllers\Api;

use App\Device;
use App\Http\Requests\Api\PurchaseRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Purchase;
use System\Database\DBBuilder\DBBuilder;
use App\Http\Controllers\Controller;
use System\Lib\Exp;
use System\Lib\JalaliDate;
use System\Config\Config;

class ApiController extends Controller {

    public function __construct()
    {
        header('Content-Type: application/json');

//        if(clearStr($_REQUEST["code"]) != md5(clearStr($_REQUEST["deviceID"]). Config::get("app.APP_KEY"))){
//            http_response_code(404);
//            exit("Invalid Request");
//        }
    }

    public function register()
    {
        try {
            //! Input validation
            $validation     = new RegisterRequest(true);
            $request        = $validation->all();
            $token          = md5($request["deviceID"] . $request["mobile"] . microtime(true));

            //! Checking user exist or not
            $exist          = Device::where("u_id",$request["deviceID"])->first();
            if ($exist){
                jsonResponse(1,__tr("Your device was registered before"));
            }

            //! insert device to DB
            $data = Device::create([
                "token"         => $token,
                "u_id"          => $request["deviceID"],
                "mobile"        => $request["mobile"],
                "os"            => getUag(),
                "ip"            => getIp()
            ]);

            if ($data){
                jsonResponse(1,__tr("Your registration was successful"));
            }else{
                jsonResponse(2,__tr("The system has a problem. Please try again"));
            }
        }catch (\Exception $e){
            new Exp($e->getMessage(),$e->getCode());
            jsonResponse(3,__tr("The system has a problem. Please try again"));
        }
    }

    public function check($token)
    {
        try {
            if (empty($token)){
                jsonResponse(0,__tr("Unfortunately, you don't have active purchase now"));
            }

            //! checking this user has active purchase or not AND expired another purchase
            $purchase  = $this->check_purchase($token);

            if ($purchase){
                $Lib_date   = new JalaliDate("America/Chicago");
                $response = [
                    "status"        => $purchase->status,
                    "expire_date"   => $Lib_date->date("Y-m-d H:i:s","en",$purchase->expire_date),
                    "receipt"       => $purchase->receipt_code,
                ];
                jsonResponse($response,__tr("Unfortunately, you don't have active purchase now"));
            }
            jsonResponse(0,__tr("Unfortunately, you don't have active purchase now"));
        }catch (\Exception $e){
            new Exp($e->getMessage(),$e->getCode());
            jsonResponse(3,__tr("The system has a problem. Please try again"));
        }
    }

    public function purchase()
    {
        try {
            //! Input validation
            $validation     = new PurchaseRequest(true);
            $request        = $validation->all();

            //! check authentication
            $device_row     = Device::where("token",$request["token"])->first();
            if (empty($device_row->token)){
                jsonResponse(0,__tr("authentication was failed"));
            }

            $Lib_date       = new JalaliDate("America/Chicago");

            //! checking user has active purchase or not
            $hasPurchase = $this->check_purchase($request["token"]);
            if ($hasPurchase){
                $response = [
                    "status"        => $hasPurchase->status,
                    "message"       => __tr("Your have a active purchase"),
                    "expire_date"   => $Lib_date->date("Y-m-d H:i:s","en",$hasPurchase->expire_date),
                    "receipt"       => $hasPurchase->receipt_code,
                ];
                jsonResponse($response,__tr("Unfortunately, Your purchase failed"));
            }

            //! checking this receipt_code exist in DB or not
            $receipt_row    = Purchase::where("receipt_code",$request["receipt"])->first();
            if ($receipt_row){
                if ($receipt_row->status == 1){
                    $response = [
                        "status"        => $receipt_row->status,
                        "message"       => __tr("Your purchase was completed successfully"),
                        "expire_date"   => $Lib_date->date("Y-m-d H:i:s","en",$receipt_row->expire_date),
                        "receipt"       => $request["receipt"],
                    ];
                }else{
                    $response = 0;
                }
                jsonResponse($response,__tr("Unfortunately, Your purchase failed"));
            }

            //! checking this receipt_code succeed or not
            $status = checkPurchase($request["receipt"]);
            if($status) {
                $time   = time() + (3600 * 24 * 30 * rand(1,12));
                $data   = Purchase::create([
                    "device_id"     => $device_row->id,
                    "token"         => $device_row->token,
                    "receipt_code"  => $request["receipt"],
                    "status"        => $status,
                    "expire_date"   => $time,
                ]);
                if (!$data){
                    jsonResponse(2,__tr("The system has a problem. Please try again"));
                }
                $response = [
                    "status"        => $status,
                    "message"       => __tr("Your purchase was completed successfully"),
                    "expire_date"   => $Lib_date->date("Y-m-d H:i:s","en",$time),
                    "receipt"       => $request["receipt"],
                ];
                jsonResponse($response);
            }else{
                jsonResponse(0,__tr("Unfortunately, Your purchase failed"));
            }
        }catch (\Exception $e){
            new Exp($e->getMessage(),$e->getCode());
            jsonResponse(3,__tr("The system has a problem. Please try again"));
        }
    }

    public function execute()
    {
        //! run migration
        $tables = new DBBuilder();
    }

    private function check_purchase($token){
        $purchases    = Purchase::where("token",clearStr($token))->where("status",1)->get();
        if ($purchases){
            foreach ($purchases as $purchase){
                if ($purchase->expire_date < time()){
                    Purchase::find($purchase->id)->update(["status" => 2]);
                    continue;
                }
                return $purchase;
            }
        }
        return false;
    }

    //        <script>top.location.href="igp://?purchdone"</script>
    //        <script>top.location.href="intent:#Intent;action=com.partodesign.igp.launchfrombrowser;category=android.intent.category.DEFAULT;category=android.intent.category.BROWSABLE;S.msg_from_browser=1;end"</script>

}