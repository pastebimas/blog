<?php namespace Serverfireteam\blog\panel;

use MyWedding\Http\Controllers\Admin\usersController;
use MyWedding\Users;
use Serverfireteam\Panel\CrudController;
use \Illuminate\Routing\Controllers;

class BlogController extends \Serverfireteam\Panel\CrudController {

    public function all($entity) {

        parent::all($entity);
		// gerasis 6+++
        $this->filter = \DataFilter::source(new \App\Blog());
        $this->filter->add('id', 'ID', 'text');
        $this->filter->add('title', 'Pavadinimas', 'text');
        $this->filter->add('public','Statusas','select')->options(["" => "Statusas", 1 => "Rodoma", 0 => "Nerodoma" ]);
        $this->filter->submit('Ieškoti');
        $this->filter->reset('Išvalyti');
        $this->filter->build();

        $this->grid = \DataGrid::source($this->filter);
        $this->grid->add('id', 'ID', true)->style("width:100px");
        $this->grid->add('title', 'Pavadinimas', true);
        $this->grid->add('created_at','Sukūrimo data');
        $this->grid->add('public', 'Statusas');
        $this->addStylesToGrid();

        $this->grid->edit('edit/', 'Redaguoti','modify|delete');
        $this->grid->link('edit',"Naujas įrašas", "BR");
        $this->grid->orderBy('id','desc');
        $this->grid->paginate(10);


        $this->grid->row(function ($row) {
            if ($row->cell('public')->value == 1) {
                $row->cell('public')->value = "Rodomas";
            } elseif ($row->cell('public')->value == 0 ) {
                $row->cell('public')->value = "Nerodomas";
                $row->cell('public')->style("font-weight:bold; color:red;");
            }
        });



        return $this->returnView();
    }

    public function edit($entity) {

        parent::edit($entity);

        $this->edit = \DataEdit::source(new \App\Blog());

        $this->edit->label('Blogo įrašai');
        $this->edit->add('title', 'Pavadinimas', 'text')->rule('required|min:3');
      //  $this->edit->add('author', 'author', 'text')->rule('required|min:2');

        $this->edit->add('pamineti_fotografai','Paminėtas fotografas','autocomplete')->options(Users::lists('name', 'id')->all());

     //   $this->edit->add('users.email','Users','tags')->remote("fullname", "id", "\MyWedding\Http\Controllers\Admin\usersController\getAutocomplete");

        $this->edit->add('content', 'Tekstas', 'redactor')->rule('required');
        $this->edit->add('image', 'Nuotrauka', 'image')->move('uploads/');
        //$this->edit->add('color', 'Color', 'colorpicker');
        $this->edit->add('public', 'Statusas', 'radiogroup')->option(0, 'Tik išsaugoti')->option(1, 'Atvaizduoti');

        return $this->returnEditView();
    }
}