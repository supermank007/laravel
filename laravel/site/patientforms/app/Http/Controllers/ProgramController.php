<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Program;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $programs = Program::all();
        return view('admin/programs-index', ['programs' => $programs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.create_new_program');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
        ]);

        $program = new Program;

        $program->name = $request->name;
        $program->description = $request->description;

        $program->save();

        return redirect()->route('programs.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Program $program)
    {
        return view('admin.create_new_program', ['program' => $program]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Program $program)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'required',
        ]);

        $program->name = $request->name;
        $program->description = $request->description;

        $program->save();

        return redirect()->route('programs.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Program $program)
    {

        if ($request->action == "move_users") {

            $new_program = Program::findOrFail($request->new_program);

            $users = $program->users()->get();
            foreach ($users as $user) {
                $user->program()->associate($new_program);
                $user->save();
            }
        } elseif ($request->action == 'delete_users') {

            $users = $program->users()->get();
            foreach ($users as $user) {
                $user->delete();
            }

        }
        
        $program->delete();
        return "program deleted";

    }

    public function deleteModal(Program $program)
    {
        $users = $program->users()->get();
        $programs = Program::all();
        return view('programs.delete-modal',
            [
                'currentProgram' => $program,
                'users'   => $users,
                'programs'=> $programs,
            ]
        );

    }
}
