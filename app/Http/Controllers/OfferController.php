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

        $rules = [
            'offerId' => 'required|integer',
        ];
        $messages = [
            'offerId.required' => 'Обязательное поле',
            'offerId.integer' => 'Не является числом'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->getMessageBag()->toArray();
            $firstErrorKey = array_key_first($errors);
            return $this->generateJsonResponse('error', $firstErrorKey, $errors[$firstErrorKey][0]);
        }
        $validatedData = $validator->validated();

        $offer = Offers::where('id', '=', $validatedData['offerId'])->first();
        $userVotes = unserialize($offer->votes);

        if ( empty($userVotes) ){
            $userVotes = [Auth::user()->id];
        }else{
            array_push($userVotes, Auth::user()->id);
        }

        $offer->votes = serialize($userVotes);
        $offer->save();

        return $this->generateJsonResponse('success', count($userVotes), $request->offerId);
    }
}
