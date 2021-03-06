<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

use Auth;
use View;
use PDF;
use Storage;
use File;

use \App\Incidencia;
use \App\PrioritatIncidencia;
use \App\Producte;
use \App\Tipus_producte;
use \App\Atributs_producte;
use \App\Cistella;
use \App\Linia_cistella;
use \App\Venta_productes;
use \App\Linia_ventes;
use \App\noticies;
use \App\categories;
use \App\Votacio_user_atraccio;
use \App\Atraccion;
use \App\Promocions;
use \App\User;
use \App\TipusAtraccions;
use \App\Contacte;
use \App\Linia_contacte;
use \App\Chat;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Mostra la pàgina inicial de l'aplicatiu i hi carrega notícies.
     *
     * @return /Illuminate/Http/Response
     */
    public function index()
    {
        $noticies = DB::table('noticies')
            ->join('users', 'users.id', '=', 'noticies.id_usuari')
            ->join('categories', 'categories.id', '=', 'noticies.categoria')
            ->select('noticies.id', 'noticies.str_slug', 'titol', 'descripcio', 'users.nom', 'users.cognom1', 'users.cognom2', 'users.numero_document', 'path_img', 'categories.nom as categoria', 'categories.id as catId')
            ->orderBy('id', 'DESC')
            ->paginate(2);

        return view('index', compact('noticies'));
    }

    /**
     * Mostra la pàgina pública d'atraccions amb les dades de les atraccions de la base de dades.
     *
     * @return /Illuminate/Http/Response
     */
    public function atraccions()
    {
        $atraccionetes = TipusAtraccions::join('atraccions', 'atraccions.tipus_atraccio', '=', 'tipus_atraccions.id')
            ->get([
                'tipus_atraccions.tipus as nom',
                'tipus_atraccions.id as id_tipus',
                'atraccions.nom_atraccio',
                'atraccions.tipus_atraccio',
                'atraccions.data_inauguracio',
                'atraccions.altura_min',
                'atraccions.altura_max',
                'atraccions.accessibilitat',
                'atraccions.acces_express',
                'atraccions.id',
                'atraccions.path',
                'atraccions.descripcio'
            ]);

        return view("atraccions", compact('atraccionetes'));
    }

    /**
     * Retorna la vista entrades de la pàgina pública (/entrades).
     *
     * @return /Illuminate/Http/Response
     */
    public function entrades()
    {
        $tipus_producte = Tipus_producte::whereIn('id', [1, 2, 3, 4, 5, 6, 7])->get();

        return view("entrades", compact('tipus_producte'));
    }

    /**
     * Retorna la vista del login de la pàgina pública (/login).
     *
     * @return /Illuminate/Http/Response
     */
    public function login()
    {
        return view("login");
    }

    /**
     * Retorna la vista del contacte de la pàgina pública (/contacte).
     *
     * @return /Illuminate/Http/Response
     */
    public function contacte()
    {
        $chat = Chat::all();

        return view("contacte", compact('chat'));
    }

    /**
     * Retorna la vista inicial de la gestió pàgina interna (/gestio).
     *
     * @return /Illuminate/Http/Response
     */
    public function gestio()
    {
        return view("gestio/index");
    }

    /**
     * Retorna la vista del perfil de l'usuari de la pàgina pública (/perfil).
     *
     * @return /Illuminate/Http/Response
     */
    public function perfil()
    {
        return view('perfil');
    }

    // public function mes()
    // {
    //     return view('mes');
    // }

    // public function pizzeria()
    // {
    //     return view('pizzeria');
    // }

    /**
     * Retorna la vista de les Frequently Asked Questions de la pàgina pública (/faq).
     *
     * @return /Illuminate/Http/Response
     */
    public function faq()
    {
        return view('faq');
    }

    // public function multimedia()
    // {
    //     return view('multimedia');
    // }

    /**
     * Retorna la vista de les Condicions Generals de la pàgina pública (/condicionsgenerals).
     *
     * @return /Illuminate/Http/Response
     */
    public function condicionsgenerals()
    {
        return view('condicionsgenerals');
    }

    /**
     * Retorna la vista de Politica de privacitat de la pàgina pública (/politicaprivacitat).
     *
     * @return /Illuminate/Http/Response
     */
    public function politicaprivacitat()
    {
        return view('politicaprivacitat');
    }

    /**
     * Retorna la vista de Politica de cookies de la pàgina pública (/politicacookies).
     *
     * @return /Illuminate/Http/Response
     */
    public function politicacookies()
    {
        return view('politicacookies');
    }

    /**
     * Retorna la vista de Qui Som de la pàgina pública (/qui-som).
     *
     * @return /Illuminate/Http/Response
     */
    public function equipdirectiu()
    {
        return view('equipdirectiu');
    }

    /**
     * Retorna la vista de la visita digital de la pàgina pública (/visita-digital).
     *
     * @return /Illuminate/Http/Response
     */
    public function visitadigital()
    {
        return view('visitadigital');
    }

    /**
     * Retorna la vista per reportar una incidència de la pàgina pública - només usuaris autentificats (/incidencia).
     *
     * @return /Illuminate/Http/Response
     */
    public function incidencia()
    {
        $prioritats = PrioritatIncidencia::all();

        return view('incidencia', compact('prioritats'));
    }

    /**
     * Retorna la vista de les tasques dels treballadors de la pàgina interna (/tasques).
     *
     * @return /Illuminate/Http/Response
     */
    public function tasques()
    {
        $user = Auth::user();

        $incidencies_per_fer = Incidencia::where('id_usuari_assignat', $user->id)
            ->where('id_estat', 2)
            ->join('tipus_prioritat', 'incidencies.id_prioritat', 'tipus_prioritat.id')
            ->join('estat_incidencies', 'incidencies.id_estat', 'estat_incidencies.id')
            ->get([
                'incidencies.id as id',
                'incidencies.titol as titol',
                'incidencies.descripcio as descripcio',
                'tipus_prioritat.nom_prioritat as nom_prioritat',
                'estat_incidencies.nom_estat as nom_estat',
            ]);

        $assignacio = DB::table('assign_emp_atraccions')
            ->leftJoin('users', 'users.id', 'assign_emp_atraccions.id_empleat')
            ->leftJoin('atraccions', 'atraccions.id', 'assign_emp_atraccions.id_atraccio')
            ->leftJoin('rols', 'rols.id', 'users.id')
            ->where('users.id', $user->id)
            ->get([
                'assign_emp_atraccions.id as id',
                'assign_emp_atraccions.id_empleat as id_empleat',
                'assign_emp_atraccions.id_atraccio as id_atraccio',
                'assign_emp_atraccions.data_inici as data_inici',
                'assign_emp_atraccions.data_fi as data_fi',
                'users.nom as nom_empleat',
                'users.cognom1 as cognom_empleat',
                'atraccions.nom_atraccio as nom_atraccio',
                'rols.nom_rol as nom_rol',
                'atraccions.id as id_atra'
            ]);

        $tiquets = Linia_Contacte::where('id_empleat', $user->id)
            ->where('id_estat', 2)
            ->leftJoin('contacte', 'contacte.id', 'linia_contacte.id_ticket_contacte')
            ->get([
                'contacte.id as id',
                'contacte.nom as nom',
                'contacte.email as email',
                'contacte.tipus_pregunta as tipus_pregunta',
                'contacte.missatge as missatge'
            ]);

        return view('tasques', compact(['incidencies_per_fer', 'assignacio', 'tiquets']));
    }

    /**
     * Acció que realiza els càlculs quan s'afegeix un producte a la cistella.
     *
     * @return /Illuminate/Http/Response
     */
    public function parc_afegir_cistella(Request $request)
    {
        if (Auth::check() == true) {
            /*si el tipus 6 o 7 rep tiquet numero de viatges*/
            if ($request->get('tipus_select') == 6 || $request->get('tipus_select') == 7) {
                /*Si es 3 te 3 viatges si no es 6 o 7 e*/
                if ($request->get('num_viatges') == 3) {

                    $preu_base = Tipus_producte::find($request->get('tipus_select'))->preu_base;
                    $preu_final = $preu_base + 5;
                    $viatges = $request->get('num_viatges');
                } elseif ($request->get('num_viatges') == 6) {
                    $preu_base = Tipus_producte::find($request->get('tipus_select'))->preu_base;
                    $preu_final = $preu_base + 10;
                    $viatges = $request->get('num_viatges');
                }
                /*Si no es 6 o 7 tindra 100 viatges*/
            } else {
                $preu_final = Tipus_producte::find($request->get('tipus_select'))->preu_base;
                $viatges = 100;
            }

            /*devolucio no funciona estat defecte*/
            $estat_defecte = 1;

            /*insert taula atributs*/
            $atributs_producte = new Atributs_producte([
                'nom' => $request->get('tipus_select'),
                'tickets_viatges' => $viatges,
                'preu' => $preu_final

            ]);

            $atributs_producte->save();

            /*Després es guarda el producte*/
            $producte = new producte([
                'atributs' => $atributs_producte->id,
                'estat' => $estat_defecte,
                'descripcio' => " "
            ]);

            $producte->save();

            /*Generacio de la entrada en lo codi QR*/
            $file_path = 'storage/tickets_parc';

            //Comprovació de que la carpeta on es guardaràn les imatges dels productes existeis i si no crearla
            if( ! File::exists($file_path)) {
                File::makeDirectory($file_path, 0775, true);
            }

            $file_name_path =  $file_path . '/' . time() . $producte->id . '.png';
            $imatge = QrCode::format('png')->size(399)->color(40, 40, 40)->generate($producte->id, $file_name_path);
            //$imatge = QrCode::format('png')->size(399)->color(40,40,40)->generate('a', 'storage/tickets_parc/a.png');

            /*Afegim el codi QR a la base de dades*/
            DB::table('atributs_producte')
                ->where('id', $atributs_producte->id)
                ->update(['foto_path' => $file_name_path]);

            /*Afegir producte a la cistella*/
            /*Comprovem si existeix la cistella i si no existeix la creem*/
            if (Cistella::where('id_usuari', '=', Auth::id())->count() > 0) {

                $cistella = DB::table('cistelles')
                    ->where('id_usuari', '=', Auth::id())
                    ->first();
                $linia_cistella = new Linia_cistella([
                    'id_cistella' => $cistella->id,
                    'producte' => $producte->id,
                    'quantitat' => $request->get('quantitat')
                ]);
                $linia_cistella->save();

                /*Creem la cistella si no existeix*/
            } else {

                $cistella = new Cistella([
                    'id_usuari' => Auth::id()
                ]);
                $cistella->save();

                $linia_cistella = new Linia_cistella([
                    'id_cistella' => $cistella->id,
                    'producte' => $producte->id,
                    'quantitat' => $request->get('quantitat')
                ]);
                $linia_cistella->save();
            }
        }

        return redirect('/cistella')->with('success', 'Ticket afegit a la cistella correctament');
    }

    /**
     * Retorna la vista de la compra finalitzada i processa els productes de la cistella per a convertir-los a ventes
     * @return /Illuminate/Http/Response
     */
    public function compra_finalitzada()
    {
        $quantitat_cistella = Linia_cistella::join('cistelles', 'cistelles.id', '=', 'linia_cistelles.id_cistella')
            ->where('cistelles.id_usuari', '=', Auth::id())->count();
        //Comprova si hi han productes a la cistella del usuari
        if ($quantitat_cistella > 0) {
            $elements_cistella = DB::table('linia_cistelles')
                ->join('cistelles', 'linia_cistelles.id_cistella', '=', 'cistelles.id')
                ->join('productes', 'linia_cistelles.producte', '=', 'productes.id')
                ->join('atributs_producte', 'productes.atributs', '=', 'atributs_producte.id')
                ->join('tipus_producte', 'atributs_producte.nom', '=', 'tipus_producte.id')
                ->select('cistelles.id_usuari as id_usuari', 'cistelles.id as id_cistella_orig', 'linia_cistelles.id as id_linia', 'linia_cistelles.id_cistella as id_cistella_linia', 'linia_cistelles.producte as producte', 'linia_cistelles.quantitat as quantitat', 'atributs_producte.preu as preu', 'atributs_producte.tickets_viatges as viatges', 'tipus_producte.id as tipus')
                ->where('cistelles.id_usuari', '=', Auth::id())
                ->get();
            //dd($elements_cistella);
            $preu_total = 0;

            foreach ($elements_cistella as $element_cistella) {
                //dd($element_cistella->preu);
                $preu_total = $preu_total + ($element_cistella->preu * $element_cistella->quantitat);
            }

            $venta = new Venta_productes([
                'id_usuari' => Auth::id(),
                'preu_total' => $preu_total,
                'estat' => 1
            ]);

            $venta->save();
            //Canvía els productes amb quantitat X a 1 i genera els restants
            foreach ($elements_cistella as $element_cistella) {

                if ($element_cistella->tipus == 1 || $element_cistella->tipus == 2 || $element_cistella->tipus == 3 || $element_cistella->tipus == 4 || $element_cistella->tipus == 5 || $element_cistella->tipus == 6 || $element_cistella->tipus == 7) {
                    $linia_venta_original = new Linia_ventes([
                        'id_venta' => $venta->id,
                        'producte' => $element_cistella->producte,
                        'quantitat' => 1
                    ]);
                    $linia_venta_original->save();
                    for ($i = 0; $i < $element_cistella->quantitat - 1; $i++) {
                        $atributs_producte_ticket = new Atributs_producte([
                            'nom' => $element_cistella->tipus,
                            'tickets_viatges' => $element_cistella->viatges,
                            'preu' => $element_cistella->preu
                        ]);

                        $atributs_producte_ticket->save();

                        /*Després es guarda el producte*/
                        $producte_ticket = new producte([
                            'atributs' => $atributs_producte_ticket->id,
                            'estat' => 1,
                            'descripcio' => " "
                        ]);

                        $producte_ticket->save();

                        /*Generacio de la entrada en lo codi QR*/
                        $file_path_ticket = 'storage/tickets_parc';
                        $file_name_path_ticket =  $file_path_ticket . '/' . time() . $producte_ticket->id . '.png';
                        $imatge_ticket = QrCode::format('png')->size(399)->color(40, 40, 40)->generate($producte_ticket->id, $file_name_path_ticket);

                        /*Afegim el codi QR a la base de dades*/
                        DB::table('atributs_producte')
                            ->where('id', $atributs_producte_ticket->id)
                            ->update(['foto_path' => $file_name_path_ticket]);

                        $linia_venta = new Linia_ventes([
                            'id_venta' => $venta->id,
                            'producte' => $producte_ticket->id,
                            'quantitat' => 1
                        ]);
                        $linia_venta->save();
                    }
                    $linia_cistella_element = Linia_cistella::find($element_cistella->id_linia);
                    $linia_cistella_element->delete();
                } else {
                    $linia_venta = new Linia_ventes([
                        'id_venta' => $venta->id,
                        'producte' => $element_cistella->producte,
                        'quantitat' => $element_cistella->quantitat
                    ]);
                    $linia_venta->save();
                    $linia_cistella_element = Linia_cistella::find($element_cistella->id_linia);
                    $linia_cistella_element->delete();
                }
            }

            /*GENERACIÓ DE FACTURA + ENVIARMENT CORREU EN SEGON PLA*/
            $id_venta = $venta->id;

            $usuari = Auth::user();

            dispatch(new \App\Jobs\GenerateFacturaPDFJob($id_venta, $usuari));

            return view('/compra_finalitzada', compact('venta'));
        } else {
            return redirect('/cistella')->with('error', 'No tens cap producte a la cistella.');
        }
    }

    /**
     * Retorna la vista de la cistella, on apareixen els tickets i fotos dividits en dues taules
     * @return /Illuminate/Http/Response
     */
    public function cistella()
    {
        //tickets
        $linia_cistella = Cistella::join('linia_cistelles', 'linia_cistelles.id_cistella', '=', 'cistelles.id')
            ->join('productes', 'linia_cistelles.producte', '=', 'productes.id')
            ->join('atributs_producte', 'productes.atributs', '=', 'atributs_producte.id')
            ->join('tipus_producte', 'atributs_producte.nom', '=', 'tipus_producte.id')
            ->select('linia_cistelles.id as id', 'tipus_producte.nom as nom', 'atributs_producte.tickets_viatges as tickets_viatges', 'atributs_producte.mida as mida', 'atributs_producte.preu as preu', 'linia_cistelles.quantitat as quantitat', 'tipus_producte.id as tipus')
            ->where('cistelles.id_usuari', '=', Auth::id())
            ->whereIn('tipus_producte.id', [1, 2, 3, 4, 5, 6, 7])
            ->orderBy('nom', 'ASC')
            ->get([
                'linia_cistelles.id as id',
                'atributs_producte.id as id_atributs',
                'tipus_producte.nom as nom',
                'atributs_producte.tickets_viatges as tickets_viatges',
                'atributs_producte.mida as mida',
                'atributs_producte.preu as preu',
                'linia_cistelles.quantitat as quantitat',
                'tipus_producte.id as tipus'
            ]);

        $total = 0;
        $total2 = 0;
        $compteTotal = 0;

        //fotos
        $fotos = DB::table('cistelles')
            ->join('linia_cistelles', 'linia_cistelles.id_cistella', '=', 'cistelles.id')
            ->join('productes', 'linia_cistelles.producte', '=', 'productes.id')
            ->join('atributs_producte', 'productes.atributs', '=', 'atributs_producte.id')
            ->join('tipus_producte', 'atributs_producte.nom', '=', 'tipus_producte.id')
            ->select('linia_cistelles.id as id', 'tipus_producte.nom as nom', 'atributs_producte.tickets_viatges as tickets_viatges', 'atributs_producte.mida as mida', 'atributs_producte.preu as preu', 'atributs_producte.foto_path_aigua as fotoaigua', 'atributs_producte.thumbnail as thumbnail', 'linia_cistelles.quantitat as quantitat')
            ->where('cistelles.id_usuari', '=', Auth::id())
            ->where('tipus_producte.id', '=', 8)
            ->orderBy('nom', 'ASC')
            ->get();

        $user = Auth::user();

        return view('/cistella', compact('linia_cistella', 'total', 'fotos', 'total2', 'compteTotal', 'user'));
    }

    /**
     * Elimina un producte de la cistella, depenent del tipus del mateix realiza una acció o un altra
     * @param  Request $request [description]
     * @return /Illuminate/Http/Response
     */
    public function cistella_delete(Request $request)
    {
        $linia_cistella = Linia_cistella::find($request->get('id_linia_producte'));
        $producte = producte::find($linia_cistella->producte);
        $atributs_producte = Atributs_producte::find($producte->atributs);
        $tipus_producte = Tipus_producte::find($atributs_producte->nom);

        $linia_cistella->delete();

        //Si es un ticket, també elimina el producte
        if ($tipus_producte->id == 1 || $tipus_producte->id == 2 || $tipus_producte->id == 3 || $tipus_producte->id == 4 || $tipus_producte->id == 5 || $tipus_producte->id == 6 || $tipus_producte->id == 7) {
            $producte->delete();
            $atributs_producte->delete();
        }

        return redirect('/cistella')->with('success', 'Producte eliminat correctament');
    }

    /**
     * Retorna la vista de la compra, on apareixen les diferents opcions de pagament
     * @return /Illuminate/Http/Response
     */
    public function compra()
    {
        $quantitat_cistella = Linia_cistella::join('cistelles', 'cistelles.id', '=', 'linia_cistelles.id_cistella')
            ->where('cistelles.id_usuari', '=', Auth::id())->count();
        if ($quantitat_cistella > 0) {
            $linia_cistella = Cistella::join('linia_cistelles', 'linia_cistelles.id_cistella', '=', 'cistelles.id')
                ->join('productes', 'linia_cistelles.producte', '=', 'productes.id')
                ->join('atributs_producte', 'productes.atributs', '=', 'atributs_producte.id')
                ->join('tipus_producte', 'atributs_producte.nom', '=', 'tipus_producte.id')
                ->select('linia_cistelles.id as id', 'tipus_producte.nom as nom', 'atributs_producte.tickets_viatges as tickets_viatges', 'atributs_producte.mida as mida', 'atributs_producte.preu as preu', 'linia_cistelles.quantitat as quantitat')
                ->where('cistelles.id_usuari', '=', Auth::id())
                ->where('atributs_producte.foto_path_aigua', '=', null)
                ->orderBy('nom', 'ASC')
                ->paginate(100);

            $numeroTickets = $linia_cistella->count();

            $total = 0;
            $total2 = 0;
            $compteTotal = 0;

            $fotos = DB::table('cistelles')

                ->join('linia_cistelles', 'linia_cistelles.id_cistella', '=', 'cistelles.id')
                ->join('productes', 'linia_cistelles.producte', '=', 'productes.id')
                ->join('atributs_producte', 'productes.atributs', '=', 'atributs_producte.id')
                ->join('tipus_producte', 'atributs_producte.nom', '=', 'tipus_producte.id')
                ->select('linia_cistelles.id as id', 'tipus_producte.nom as nom', 'atributs_producte.tickets_viatges as tickets_viatges', 'atributs_producte.mida as mida', 'atributs_producte.preu as preu', 'atributs_producte.foto_path_aigua as fotoaigua', 'linia_cistelles.quantitat as quantitat')
                ->where('cistelles.id_usuari', '=', Auth::id())
                ->where('atributs_producte.foto_path_aigua', '!=', null)
                ->orderBy('nom', 'ASC')
                ->paginate(100);

            $numeroFotos = $fotos->count();

            $user = Auth::user();

            $usuari = User::where('id', '=', Auth::id());

            return view('/compra', compact('linia_cistella', 'total', 'fotos', 'total2', 'compteTotal', 'numeroFotos', 'numeroTickets', 'user'));
        } else {
            return redirect('/cistella')->with('error', 'No tens cap producte a la cistella.');
        }
    }

    /**
     *  Acció que llista totes les atraccions en la part pública de l'aplicatiu.
     *
     * @return /Illuminate/Http/Response
     */
    public function llistarAtraccionsPublic($id)
    {
        $atraccions = Atraccion::findOrFail($id);

        $tipus_atraccio = TipusAtraccions::findOrFail($atraccions->tipus_atraccio);

        return view('/atraccions_generades', compact('atraccions', 'tipus_atraccio'));
    }

    // /**
    //  * Acció
    //  *
    //  * @return /Illuminate/Http/Response
    //  */
    // public function tendes_inter()
    // {
    //     return view("/tendes");
    // }

    // public function tenda_figures()
    // {
    //     return view("/tenda_figures");
    // }


    /**
     *  Acció que carrega una notícia mitjançant un slug.
     *
     * @param string $str_slug
     * @return /Illuminate/Http/Response
     */
    public function noticia($str_slug)
    {
        $valid = 0;
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->id_rol == 2) {
                $valid = 1;
            }
        }

        $noticia = noticies::where('str_slug', $str_slug)->first();

        $categoria = categories::find($noticia->categoria);

        return view("/noticia", compact('noticia', 'categoria', 'valid'));
    }

    /**
     * Acció que llista totes les notícies i les diverses categories.
     *
     * @param Request $request
     * @return /Illuminate/Http/Response
     */
    public function noticies(Request $request)
    {
        $noticies = DB::table('noticies')
            ->join('users', 'users.id', '=', 'noticies.id_usuari')
            ->join('categories', 'categories.id', '=', 'noticies.categoria')
            ->select('noticies.id', 'titol', 'descripcio', 'users.nom', 'users.cognom1', 'users.cognom2', 'users.numero_document', 'path_img', 'str_slug', 'categories.nom as categoria', 'categories.id as catId')
            ->orderBy('id', 'DESC')

            ->where(function ($noticies) use ($request) {
                if ($request->has('catId')) {
                    $cat = $request->get('catId');
                    $noticies->where('categories.id', '=', $cat);
                } else {
                    $cat = "";
                }
            })

            //$noticies->where('categories.id', '=', $cat);
            ->paginate(8);

        return view('noticies', compact('noticies'));
    }

    /**
     * Acció que mostra la promoció mitjançant un slug.
     *
     * @param string $slug
     * @return /Illuminate/Http/Response
     */
    public function promocio($slug)
    {
        $valid = 0;
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->id_rol == 2) {
                $valid = 1;
            }
        }

        $promocio = promocions::where('slug', '=', $slug)->firstOrFail();;

        return view("/promocio", compact('promocio', 'valid'));
    }

    /**
     * Acció que llista promocions en la pàgina web pública.
     *
     * @param Request $request
     * @return /Illuminate/Http/Response
     */
    public function promocions(Request $request)
    {
        $promocions = DB::table('promocions')
            ->join('users', 'users.id', '=', 'promocions.id_usuari')
            ->select('promocions.id', 'titol', 'descripcio', 'users.nom', 'users.cognom1', 'users.cognom2', 'users.numero_document', 'path_img', 'slug')
            ->orderBy('id', 'DESC')
            ->paginate(8);

        return view('promocions', compact('promocions'));
    }

    /**
     * Acció que mostra les votacions de cada atracció en el top d'atraccions de la pàgina web púlica.
     *
     * @return /Illuminate/Http/Response
     */
    public function votacions()
    {
        $atraccions = json_encode(DB::table('atraccions')
            ->select('atraccions.id as id', 'atraccions.nom_atraccio as title', 'atraccions.descripcio as description', 'atraccions.path as url', 'atraccions.votacions as votes', 'atraccions.path as avatar', 'atraccions.path as submissionImage')
            ->orderBy('id', 'DESC')
            ->get());

        return view('votacions', compact('atraccions'));
    }

    /**
     * Acció que permet votar una atracció en el top d'atraccions, només 1 vegada a l'any per usuari amb sessió iniciada.
     *
     * @param Request $request
     * @return /Illuminate/Http/Response
     */
    public function votacio_accio(Request $request)
    {
        if (Auth::check()) {

            $votacions_user = Votacio_user_atraccio::where('id_usuari', Auth::id())
                ->whereBetween('created_at', array(Carbon::now()->subDays(365)->toDateTimeString(), Carbon::now()->toDateTimeString()))
                ->count();

            if ($votacions_user > 0) {
                return redirect('/votacions')->with('error', 'Les votacions son anuals.');
            } else {
                $atraccio_votar = Atraccion::find($request->get('id_atraccio'));

                $votacio_actualitzat = $atraccio_votar->votacions + 1;

                DB::table('atraccions')
                    ->where('id', $request->get('id_atraccio'))
                    ->update(['votacions' => $votacio_actualitzat]);

                $votacio = new Votacio_user_atraccio([
                    'id_usuari' => Auth::id(),
                    'id_atraccio' => $request->get('id_atraccio')
                ]);

                $votacio->save();

                return redirect('/votacions')->with('success', 'Votació realitzada correctament');

                /*$atraccions = json_encode(DB::table('atraccions')
                    ->select('atraccions.id as id', 'atraccions.nom_atraccio as title', 'atraccions.descripcio as description', 'atraccions.path as url', 'atraccions.votacions as votes', 'atraccions.path as avatar', 'atraccions.path as submissionImage')
                    ->orderBy('id', 'DESC')
                    ->get());
                    return view('votacions', compact('atraccions'));*/
            }
        } else {
            return redirect('/votacions')->with('error', 'És necessari iniciar sessió per votar.');
        }
    }

    // public function construccio()
    // {
    //     return view('construccio');
    // }

    /**
     * Acció que modifica la quantitat d'un producte a la cistella
     * @return /Illuminate/Http/Response
     */
    public function modificar_element_cistella_ajax()
    {
        $update_linia_cistella = Linia_cistella::where('id', request('id_linia_cistella'))->update(['quantitat' => request('quantitat_mod')]);

        return 'El buen update gente';
    }

    /**
     * Acció que modifica el número de viatges d'un ticket a la cistella
     * @return /Illuminate/Http/Response
     */
    public function modificar_element_cistella_ajaxV()
    {
        $numviatges = request('quantitat_mod');
        $linea_cistella = Linia_cistella::find(request('id_atributs'));
        $producte = Producte::find($linea_cistella->producte);
        $atributs = Atributs_producte::find($producte->atributs);
        $update_atributs_viatges = Atributs_producte::where('id', $producte->atributs)->update(['tickets_viatges' => request('quantitat_mod')]);

        if ($numviatges == 3) {
            $update_atributs_preu = Atributs_producte::where('id', $producte->atributs)->update(['preu' => $atributs->preu - 5]);
        } else {
            $update_atributs_preu = Atributs_producte::where('id', $producte->atributs)->update(['preu' => $atributs->preu + 5]);
        }

        return 'El buen update de viages gente';
    }

    /**
     * Retorna la vista de la sala del chat realitzada amb ajax (arxiu de text)
     * @return /Illuminate/Http/Response
     */
    public function sala_chat()
    {
        return view('sala_chat');
    }

    /**
     * Retorna la vista del llistat de notificacions
     * @return /Illuminate/Http/Response
     */
     public function notificacionsGeneral(){
       return view('notificacions');
     }

     /*
      * [ajaxChat description]
      * @return /Illuminate/Http/Response
      *
      public function ajaxChat()
      {
          ini_set('max_execution_time', 7000);

          while (Chat::where('check', 0)->count() < 1) {
              usleep(1000);
          }

          if (Chat::where('check', 0)->count() > 0) {
              $message = Chat::where('check', 0)->first();
              $id = $message->id;
              $update = Chat::find($id);
              $update->check = 1;
              $update->save();


              return response()->json([
                  'msg' => $message->name,
              ]);
          }
      }*/
}
