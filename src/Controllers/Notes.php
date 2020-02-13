<?php 
namespace Simcify\Controllers;

use Simcify\Database;
use Simcify\Auth;

class Notes{

	public function get($notetype){
		if(strstr($notetype, "_complex")){
			return $this->get_complex($notetype);
		}
        $user = Auth::user();
        $notetype = strtolower($notetype);
		$notes = [];
		$title = $this->getTitle($notetype);
        $hinweises = Database::table('hinweis')->where('parent_id', 0)->where('hinweis_family', $notetype)->orderBy('id', false)->get();
        foreach ($hinweises as $key => $hinweischen) {
        	$hinweischen->name = $hinweischen->hinweis;
        	$noteid = $hinweischen->id;
        	$hinweischen->notes = Database::table('hinweis')->where('parent_id', $noteid)->get();
        	array_push($notes, $hinweischen);
        }
		return view('notes', compact("user", "title", "notes", "notetype"));	
	}


	/**
	 * Gets the praktischer unterricht.
	 * this page is very complex
	 * in the App, the pages are splitted into two independent parts
	 * top and bottom
	 * the top and bottom both have children notes that are also parents
	 * and simultaneously a family
	 * this children notes also have children notes that are also parents
	 * but NOT simultaneously a family
	 * 
	 * --SO the layer is---
	 * Top (family) --family> familyname = praktisch_top
	 *	--family> familyname = praktisch_top_header
	 *		--parent
	 *			--children 
	 *
	 * Bottom (family) --family
	 *	--family (parent)
	 *		--parent (children)
	 *			--children 
	 *
	 * @return     <type>  The praktischer unterricht.
	 */
	public function get_complex($notetype){
        $user = Auth::user();
        $notetype = strtolower($notetype);
		//changes e.g theorie_complex_36 to theorie_complex
		$stop = strpos($notetype, "_complex") + strlen("_complex");
		$notetype = substr($notetype, 0, $stop);
		$title = $this->getTitle($notetype);
        $notes_collections = [];

		//get top
        $complex_hinweise = Database::table('hinweis')->where('parent_id', 0)->where('hinweis_family', $notetype)->orderBy('id', false)->get();
		foreach ($complex_hinweise as $key => $complex_hinweis) {

			$complex_hinweis_id = $complex_hinweis->id;
			$complex_hinweis_family = $notetype."_".$complex_hinweis_id;

	        $hinweises = Database::table('hinweis')->where('parent_id', $complex_hinweis_id)->orderBy('id', false)->get();
	        foreach ($hinweises as $key => $hinweischen) {
	        	$hinweischen->name = $hinweischen->hinweis;
	        	$noteid = $hinweischen->id;
	        	$hinweischen->notes = Database::table('hinweis')->where('parent_id', $noteid)->get();
	        }

        	$complex_hinweis->name = $complex_hinweis->hinweis;
        	$complex_hinweis->notes = $hinweises;
        	$complex_hinweis->notetype = $complex_hinweis_family;
	        array_push($notes_collections, $complex_hinweis);
		}
		return view('practicallessons', compact("user", "title", "notes_collections", "notetype"));	
	}

	public function viewcomplexnote($notetype){
        $user = Auth::user();
        $notetype = strtolower($notetype);
		$title = $this->getTitle($notetype);
        $notes_collections = [];

		//get top
        $complex_hinweise = Database::table('hinweis')->where('parent_id', 0)->where('hinweis_family', $notetype)->orderBy('id', true)->get();
		foreach ($complex_hinweise as $key => $complex_hinweis) {

			$complex_hinweis_id = $complex_hinweis->id;
			$complex_hinweis_family = $notetype."_".$complex_hinweis_id;

	        $hinweises = Database::table('hinweis')->where('parent_id', $complex_hinweis_id)->orderBy('id', true)->get();
	        foreach ($hinweises as $key => $hinweischen) {
	        	$hinweischen->name = $hinweischen->hinweis;
	        	$noteid = $hinweischen->id;
	        	$hinweischen->notes = Database::table('hinweis')->where('parent_id', $noteid)->get();
	        }
	        
        	$complex_hinweis->name = $complex_hinweis->hinweis;
        	$complex_hinweis->notes = $hinweises;
        	$complex_hinweis->notetype = $complex_hinweis_family;
	        array_push($notes_collections, $complex_hinweis);
		}
		return view('previewnotes', compact("user", "title", "notes_collections", "notetype"));	
	}

	//only one child per parent
	public function viewsimplenote($notetype){
        $user = Auth::user();
        $notetype = strtolower($notetype);
		$title = $this->getTitle($notetype);
        $notes_collections = [];

		//get top
        $complex_hinweise = Database::table('hinweis')->where('parent_id', 0)->where('hinweis_family', $notetype)->orderBy('id', true)->get();
		foreach ($complex_hinweise as $key => $complex_hinweis) {

			$complex_hinweis_id = $complex_hinweis->id;
			$complex_hinweis_family = $notetype."_".$complex_hinweis_id;

	        $hinweises = Database::table('hinweis')->where('parent_id', $complex_hinweis_id)->orderBy('id', true)->get();
	        
        	$complex_hinweis->name = $complex_hinweis->hinweis;
        	$complex_hinweis->notes = $hinweises;
        	$complex_hinweis->notetype = $complex_hinweis_family;
	        array_push($notes_collections, $complex_hinweis);
		}
		return view('previewsimplenotes', compact("user", "title", "notes_collections", "notetype"));	
	}


	public function jsoncomplexnote($notetype){
        $user = Auth::user();
        $notetype = strtolower($notetype);
		$title = $this->getTitle($notetype);
        $notes_collections = [];

		//get top
        $complex_hinweise = Database::table('hinweis')->where('parent_id', 0)->where('hinweis_family', $notetype)->orderBy('id', true)->get();
		foreach ($complex_hinweise as $key => $complex_hinweis) {

			$complex_hinweis_id = $complex_hinweis->id;
			$complex_hinweis_family = $notetype."_".$complex_hinweis_id;

	        $hinweises = Database::table('hinweis')->where('parent_id', $complex_hinweis_id)->orderBy('id', true)->get();
	        foreach ($hinweises as $key => $hinweischen) {
	        	$hinweischen->name = $hinweischen->hinweis;
	        	$noteid = $hinweischen->id;
	        	$hinweischen->notes = Database::table('hinweis')->where('parent_id', $noteid)->get();
	        }
	        
        	$complex_hinweis->name = $complex_hinweis->hinweis;
        	$complex_hinweis->notes = $hinweises;
        	$complex_hinweis->notetype = $complex_hinweis_family;
	        array_push($notes_collections, $complex_hinweis);
		}
		return response()->json($notes_collections);
	}

	//only one child per parent
	public function jsonsimplenote($notetype){
        $user = Auth::user();
        $notetype = strtolower($notetype);
		$title = $this->getTitle($notetype);
        $notes_collections = [];

		//get top
        $complex_hinweise = Database::table('hinweis')->where('parent_id', 0)->where('hinweis_family', $notetype)->orderBy('id', true)->get();
		foreach ($complex_hinweise as $key => $complex_hinweis) {

			$complex_hinweis_id = $complex_hinweis->id;
			$complex_hinweis_family = $notetype."_".$complex_hinweis_id;

	        $hinweises = Database::table('hinweis')->where('parent_id', $complex_hinweis_id)->orderBy('id', true)->get();
	        
        	$complex_hinweis->name = $complex_hinweis->hinweis;
        	$complex_hinweis->notes = $hinweises;
        	$complex_hinweis->notetype = $complex_hinweis_family;
	        array_push($notes_collections, $complex_hinweis);
		}
		return response()->json($notes_collections);	
	}

	public function getTitle($notetype){
        $title = sch_translate("Hinweise");
		if($notetype == "ausbildung"){
	        $title = sch_translate('Hinweise zur Ausbildung');
		}else if($notetype == "tipps"){
	        $title = sch_translate('Tipps');
		}else if($notetype == "prufung"){
			$title = sch_translate("Hinweise zur Prufueng");
		}else if($notetype == "fuehrerscheinantrag"){
			$title = sch_translate("fuehrerscheinantrag");
		}else if($notetype == "theorie" || $notetype == "theorie_complex"){
	        $title = sch_translate('Theorieunterricht');
		}else if($notetype == "practicals" || $notetype == "practicals_complex"){
			$title = sch_translate("Praktischer Unterricht");
		}
		return $title;
	}

	public function update($notetype, $noteid){
        $user = Auth::user();
		$title = $this->getTitle($notetype);
		$toedit = (Database::table('hinweis')->where('id', $noteid)->first());
		$parentnoteid = isset($toedit->parent_id)?$toedit->parent_id: 0;

		$parentnoteid = intval($parentnoteid);
		$parent_note_text = "";
		if(intval($parentnoteid) != 0){
			$parent_note = (Database::table('hinweis')->where('id', $parentnoteid)->first());	
			$parent_note_text = isset($parent_note->hinweis)?$parent_note->hinweis: "";			
		}
		return view('createnotes', compact("user", "title", "notetype", "toedit", "parentnoteid", "noteid", "parent_note_text"));		
	}

	public function create($notetype, $parentnoteid){
		return $this->editpage($notetype, $noteid = 0, $parentnoteid, $delete = false);	
	}

	public function delete($notetype, $noteid){
        $user = Auth::user();
		$title = $this->getTitle($notetype);
		$toedit = (Database::table('hinweis')->where('id', $noteid)->first());
		$parentnoteid = isset($toedit->parent_id)?$toedit->parent_id: 0;
		$delete = true;

		$parentnoteid = intval($parentnoteid);
		$parent_note_text = "";
		if(intval($parentnoteid) != 0){
			$parent_note = (Database::table('hinweis')->where('id', $parentnoteid)->first());	
			$parent_note_text = isset($parent_note->hinweis)?$parent_note->hinweis: "";			
		}
		return view('createnotes', compact("user", "title", "notetype", "toedit", "parentnoteid", "noteid", "delete", "parent_note_text"));		
	}

	public function editpage($notetype, $noteid, $parentnoteid, $delete){
        $user = Auth::user();
		$title = $this->getTitle($notetype);

		$parentnoteid = intval($parentnoteid);
		$parent_note_text = "";
		if(intval($parentnoteid) != 0){
			$parent_note = (Database::table('hinweis')->where('id', $parentnoteid)->first());	
			$parent_note_text = isset($parent_note->hinweis)?$parent_note->hinweis: "";			
		}
		return view('createnotes', compact("user", "title", "notetype", "parentnoteid", "noteid", "delete", "parent_note_text"));
	}

	public function confirmdelete($notetype, $noteid){
        Database::table("hinweis")->where("id", $noteid)->delete();
		return redirect(url("")."notes/$notetype?deleted");	
	}

	public function save(){
		$hinweis_header = escape(input('note_name'));//ueberschrift
		$note_text = escape(input('note_text'));
		$parent_id = intval(escape(input('parent_id')));
		$noteid = intval(escape(input('note_id')));
		$notetype = escape(input('notetype'));
		$listtype = escape(input('listtype'));
		$listtype = trim(strtolower($listtype));
		$listtype = ($listtype == "checkbox")?"checkbox":"accordion";

        if($noteid == 0){
	        //create
			if(!empty($hinweis_header)){
				//new hinweis parent
		        $data = array(
		            'hinweis' => $hinweis_header,
		            'parent_id' => 0,
		            "hinweis_family"=>$notetype,
		            "listtype" => $listtype
		        );
		        Database::table('hinweis')->insert($data);
		        $parent_id = Database::table('hinweis')->insertId();				
			}
			if(!empty($note_text)){
				//new hinweis in parent
				//loop through all new till 200
				for($i = 0; $i < 200; $i++){
					if(!empty($note_text)){
				        $data = array(
				            'hinweis' => $note_text,
				            'parent_id' => $parent_id,
				            "hinweis_family"=>$notetype,
				            "listtype" => $listtype
				        );
				        Database::table('hinweis')->insert($data);
					}
					$note_text = escape(input('note_text_'.($i+1)));
					$listtype = escape(input('listtype_'.($i+1)));
					$listtype = trim(strtolower($listtype));
				}			
			}

	        $hinweisid = Database::table('hinweis')->insertId();
			return redirect(url("")."notes/".$notetype."?saved");	
        }else{
			//edit
			if(!empty($note_text)){
				//hinweis in parent
		        $data = array(
		            'hinweis' => $note_text,
		            "listtype" => $listtype
		        );
		        Database::table("hinweis")->where("id", $noteid)->update($data);	
			}
			if(!empty($hinweis_header)){
				//hinweis parent
		        $data = array(
		            'hinweis' => $hinweis_header,
		            "listtype" => $listtype
		        );		
		        Database::table("hinweis")->where("id", $noteid)->update($data);
				return redirect(url("")."notes/".$notetype."?saved");
			}	
			$hinweisid = $noteid;
        }
		return redirect(url("")."editnote/".$notetype."/".$hinweisid."?saved");
	}

	public function getDemoNotes(){

		$notesjson = '[
			  {
			    "name": "Hinweise 1",
			    "notes": [
			      {
			        "text": "HAUPTMEdeshdfeahdrfshdfasdhjdhjdsdshdshdshkdsjdkjdskjdskjdkjdskjdskjdskjds djdkjdfkjfkj jdjfkjdfkjN",
			        "id": 34,
			        "parent_id": 1
			      },
			      {
			        "text": "job",
			        "id": 35,
			        "parent_id": 1
			      },
			      {
			        "text": "test",
			        "id": 36,
			        "parent_id": 1
			      }
			    ],
			    "id": 1,
		        "parent_id": 0
			  },
			  {
			    "name": "Hinweise 2",
			    "notes": [
			      {
			        "text": "craft",
			        "id": 34
			      },
			      {
			        "text": "job",
			        "id": 35
			      },
			      {
			        "text": "test",
			        "id": 36
			      }
			    ],
			    "id": 2
			  },
			  {
			    "name": "Hinweise 3",
			    "notes": [
			      {
			        "text": "craft",
			        "id": 34
			      },
			      {
			        "text": "job",
			        "id": 35
			      },
			      {
			        "text": "test",
			        "id": 36
			      }
			    ],
			    "id": 3
			  }
			]';
		$notes = [];
		$notes = json_decode($notesjson);
		return $notes;
	}

	public function hidden(){
        $user = Auth::user();
		return view("hidden", compact("user"));
	}
}