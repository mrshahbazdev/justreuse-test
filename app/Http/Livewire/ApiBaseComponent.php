<?php

namespace App\Http\Livewire;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Http\Request;

class ApiBaseComponent extends Component {

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result) {
        $response = [
            'success' => true,
            'code' => 200,
            'data' => $result,
        ];
        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($message) {
        $response = [
            'success' => false,
            'message' => $message,
            'code' => 0
        ];
        return response()->json($response, 404);
    }



}
