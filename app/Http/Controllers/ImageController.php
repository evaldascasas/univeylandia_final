<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use \App\Atraccion;
use \App\Imatge;
use \App\Tipus_producte;
use \App\Producte;

use Image;
use File;

class ImageController extends Controller
{
	/**
	 * Retorna les dades necessàries per crear la galeria de fotos.
	 * Retorna una col·lecció amb les imatges pujades al servidor, amb el thumbnail i la foto amb marca d'aigua.
	 *
	 * @return /Illuminate/Http/Response
	 */
	public function index()
	{
		$images = Imatge::join('atraccions', 'id_atraccio', 'atraccions.id')
			->get([
				'atributs_producte.id as id',
				'atributs_producte.foto_path as foto_path',
				'atributs_producte.foto_path_aigua as foto_path_aigua',
				'atributs_producte.thumbnail as thumbnail',
				'atributs_producte.created_at as created_at',
				'atraccions.nom_atraccio as nom_atraccio',
			]);

		return view('gestio/imatges/index', compact('images'));
	}

	/**
	 * Mostra el formulari per pujar imatges.
	 *
	 * @return /Illuminate/Http/Response
	 */
	public function save()
	{
		$atraccions = Atraccion::all();

		return view('gestio/imatges/upload', compact('atraccions'));
	}

	/**
	 * Guarda les imatges rebudes d'un formulari, genera un thumbnail i una imatge amb marca d'aigua per cada imatge rebuda.
	 * Crea un producte de tipus foto i els seus atributs.
	 * 
	 * @param  \Illuminate\Http\Request  $request
	 * @return /Illuminate/Http/Response
	 */
	public function upload(Request $request)
	{
		$og_dir = 'storage/clients/originals/';
		$water_dir = 'storage/clients/water/';
		$thumb_dir = 'storage/clients/thumb/';
		$watermark = public_path('/img/watermark.png');

		$preu = Tipus_producte::where('id', 8)
			->first();

		$validator = \Validator::make($request->all(), [
			'image' => 'required',
			'image.*' => 'image|mimes:jpeg,png,jpg|max:2048',
			'attraction' => 'required|numeric'
		]);

		if ($validator->fails()) {
			return response()->json([
				'errors' => $validator->errors()->all()
			]);
		}

		// comprovar si hi ha imatges en el form
		if ($request->hasFile('image')) {

			// crear directori si no existeix
			if (!File::exists($og_dir)) {
				File::makeDirectory($og_dir, 0777, true);
			}
			if (!File::exists($water_dir)) {
				File::makeDirectory($water_dir, 0777, true);
			}
			if (!File::exists($thumb_dir)) {
				File::makeDirectory($thumb_dir, 0777, true);
			}

			$images = $request->file('image');

			foreach ($images as $image) {

				$og_image = Image::make($image);

				$og_image->resize(null, 1080, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				})->encode('png', 100);

				$og_image_name = self::generateFilenameForPNG();

				$og_image->save($og_dir . $og_image_name);

				$water_img = $og_image->insert($watermark, 'center');

				$water_img_name = self::generateFilenameForPNG();

				$water_img->save($water_dir . $water_img_name);

				$thumbnail = $water_img->resize(null, 72, function ($constraint) {
					$constraint->aspectRatio();
					$constraint->upsize();
				})->encode('png', 75);

				$thumb_name = self::generateFilenameForPNG();

				$thumbnail->save($thumb_dir . $thumb_name);

				$atributs = new Imatge([
					'nom' => 8,
					'mida' => '1080 pixels',
					'foto_path' => $og_dir . $og_image_name,
					'foto_path_aigua' => $water_dir . $water_img_name,
					'thumbnail' => $thumb_dir . $thumb_name,
					'preu' => $preu->preu_base,
					'id_atraccio' => $request->get('attraction'),
				]);

				$atributs->save();

				$producte = new Producte([
					'atributs' => ($atributs->id),
					'descripcio' => 'Foto',
					'estat' => 1,
				]);

				if ($atributs->save()) {
					$producte->save();
				}
			}
		}

		return response()->json(['status' => true]);
	}

	/**
	 * Acció que genera un nom random a una foto.
	 * 
	 * @return string $filename
	 */
	public function generateFilenameForPNG()
	{
		$filename = rand(11111111, 99999999) . time() . '.png';

		return $filename;
	}
}
