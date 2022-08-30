<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use App\Exports\ExportFileUser;
use Maatwebsite\Excel\Facades\Excel;

use Spatie\Permission\Models\Role;

class UserComponent extends Component
{
    use WithPagination;

    public $nameUser, $userRole, $editRole , $editId;


    public $perPage = 3;
    public $search = "";
    public $orderBy ='id';
    public $orderAsc = 'asc';
    public $role;
    protected $abc;
    protected $users;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {

        $this->users = User::where('name', 'LIKE', "%$this->search%")
                        ->orderby($this->orderBy, $this->orderAsc)
                        ->paginate($this->perPage);

        $this->abc = User::where('name', 'LIKE', "%$this->search%")
        ->orderby($this->orderBy, $this->orderAsc);
        if($this->role != ""){
            $this->users = User::where('name', 'LIKE', "%$this->search%")
                            ->orderby($this->orderBy, $this->orderAsc)
                            ->role($this->role)
                            ->paginate($this->perPage);
        }
        
        $roles = Role::pluck('name','name')->all();
        
        // dd($roles);
        // $this->users = collect($this->users->items());
        return view('livewire.backend.users.user', ['users' => $this->users, 'roles'=>$roles]);
    }

    public function editUser($id){
        // dd($user->roles);
        $user = User::find($id);
        $this->nameUser = $user->name;
        $userRole = $user->roles->pluck('name')->all();
        $this->userRole = $userRole[0];
        $this->editId = $user->id;
    }

    public function saveEdit(){
        $user = User::find($this->editId);
        // $roles = $user->getRoleNames();
        foreach($user->roles as $role){
            $user->removeRole($role);
        }
        // $user->removeRole($roles);
        $user->assignRole($this->editRole);


    }

    public function exportUser(){
        $ex = User::where('name', 'LIKE', "%$this->search%")
        ->orderby($this->orderBy, $this->orderAsc)->paginate();
        // dd($ex);
        return Excel::download(new ExportFileUser($ex), 'users.xlsx');
    }

}
