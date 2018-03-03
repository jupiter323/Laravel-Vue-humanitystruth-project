<?php

namespace App\Http\Middleware;

use Closure;
use App\Traffic;
use \Auth;

class TrafficLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //log traffic
        Traffic::create([
            'ip' => request()->ip(),
            'action' => $request->getRequestUri(),
            'account_id' => Auth::user() ? Auth::user()->id : 0,
        ]);
        
        /*

//IF BLACKLISTED BY IP, REJECT USER CONNECTION
	if($black = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `blacklist` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "'"))) {
		mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'BLACKLISTED REDIRECT: ".$location."', CURRENT_TIMESTAMP)");
		header("Location: https://humanitystruth.com/index.php?notice=403"); //forbidden
	}

//IF BLACKLISTED BY ACCOUNT ID, REJECT USER CONNECTION
	if($id != 0 && $black = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM `blacklist` WHERE `account_id` = " . $id))) {
		mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'BLACKLISTED REDIRECT: ".$location."', CURRENT_TIMESTAMP)");
		header("Location: https://humanitystruth.com/index.php?notice=403"); //forbidden
	}
          
         
         */
        return $next($request);
    }
}
