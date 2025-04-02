<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientCredential;
use App\Providers\RouteServiceProvider;
use App\Services\ClientCredentialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


/**
 * Class ClientCredentialController
 * @package App\Http\Controllers
 */
class ClientCredentialController extends Controller
{

    /**
     * @var ClientCredentialService
     */
    protected $clientCredentialService;


    /**
     * ClientCredentialController constructor.
     * @param ClientCredentialService $clientCredentialService
     */
    public function __construct(ClientCredentialService $clientCredentialService)
    {
        $this->middleware('auth');
        $this->clientCredentialService = $clientCredentialService;

    }

    /**
     * Display the credential list.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data = $this->clientCredentialService->getList();

        return view('credentials.index', ['data' => $data]);
    }


    /**
     * Display the credential form.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('credentials.create');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
      /*  $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);*/

        $user = ClientCredential::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'api_key' => $request->api_key,
            'secret'  => $this->generateClientSecret($request->name)

        ]);

        return redirect()->route('index');
       // return redirect(RouteServiceProvider::HOME);
    }


    /**
     * Display the credential.
     *
     * @param $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $credential = $this->clientCredentialService->getCredentialById($id);
        return view('credentials.edit', compact('credential'));
    }


    /**
     * Display the credential.
     *
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $response = $this->clientCredentialService->update($request, $id);

        if($response){
            return redirect()->back()->with('message',"Credential has been updated successfully");
        }
        return redirect()->back()->with('message',"Credential updated failed! Please try again");
    }

    /**
     * @param $client
     * @return string
     */
    private function generateClientSecret($client)
    {
        $password = $client;
        $salt = 'S@lt3d!';
        $salted_password = $password.$salt;
        $hash = md5($salted_password);

        return $hash;
    }
}
