<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PessoaStoreRequest;
use App\Http\Requests\PessoaUpdateRequest;
use App\Services\PessoaServiceInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;


class PessoaController extends Controller
{
    /**
     * @var PessoaServiceInterface
     */
    private $pessoaService;

    public function __construct(PessoaServiceInterface $pessoaService)
    {
        $this->pessoaService = $pessoaService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PessoaStoreRequest $request)
    {
        $data_cep =  json_decode($this->validarCep($request->cep));
        if(isset($data_cep->cep)){
            $pessoa = $this->pessoaService->create($request->all());
            if ($pessoa) {
                return response()->json($pessoa, Response::HTTP_OK);
            }
            return response()->json($pessoa, Response::HTTP_BAD_REQUEST);
        }else{
            return json_encode("cep não localizado");
        }
       
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $pessoa = $this->pessoaService->find($id);
        if ($pessoa) {
            return response()->json($pessoa, Response::HTTP_OK);
        }
        return response()->json($pessoa, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PessoaUpdateRequest $request, $id)
    {
        $data_cep =  json_decode($this->validarCep($request->cep));
        if(isset($data_cep->cep)){
            $pessoa = $this->pessoaService->update($request->all(),$id);
            if ($pessoa) {
                return response()->json($pessoa, Response::HTTP_OK);
            }
            return response()->json($pessoa, Response::HTTP_BAD_REQUEST);
        }else{
            return json_encode("cep não localizado");
        }
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pessoa = $this->pessoaService->delete($id);
        if ($pessoa) {
            return response()->json($pessoa, Response::HTTP_OK);
        }
        return response()->json($pessoa, Response::HTTP_BAD_REQUEST);
    }

    public function validarCep($cep){  
        $ch = curl_init("https://viacep.com.br/ws/".$cep."/json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $json_dados = json_decode(curl_exec($ch));
        return $json_dados;
    }
}