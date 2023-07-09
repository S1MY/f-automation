<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;

use App\Http\Requests\OfferRequest;
use App\Models\Offers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function setOffer(OfferRequest $request){

        $rules = [
            'offer' => 'required|string',
            'developer' => 'integer|in:1,0',
        ];
        $messages = [
            'offer.required' => 'Обязательное поле',
            'offer.string' => 'Не является строкой',
            'developer.integer' => 'Не является числом',
            'developer.in' => 'Должно быть 1 или 0',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return $this->generateJsonResponse('input', $firstErrorKey, $errors[$firstErrorKey][0]);
        }
        $validatedData = $validator->validated();
        Offers::create([
            'uid'     => Auth::user()->id,
            'offer'   => $validatedData['offer'],
            'votes'   => serialize([]),
            'for_dev' => $validatedData['developer'],
        ]);
        return $this->generateJsonResponse('success', 'Ваше предложение принято', 'Предложение будет опубликовано, если оно соотвествует нашим правилам');
    }

    public function getVote(Request $request){

        $offer = Offers::where('id', '=', $request->offerId)->first();
        $array = unserialize($offer->votes);

        if ( empty($array) ){
            $array = array(Auth::user()->id);
        }else{
            array_push($array, Auth::user()->id);
        }

        $offer->votes = serialize($array);
        $offer->save();

        return response()->json([
            'from'      => 'offers',
            'title'     => 'Вы уже голосовали!',
            'now'       => count($array),
            'offer_id'  => $request->offerId
        ]);
    }
}
