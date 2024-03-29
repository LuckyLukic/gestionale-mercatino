<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Address;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Update User')]
class UpdateUser extends Component
{
    public $userId;
    public $name;
    public $surname;
    public $email;
    public $address;
    public $city;
    public $province;
    public $postalcode;

    protected $rules = [
        'name' => 'required|string',
        'surname' => 'required|string',
        'email' => 'required|email',
        'address' => 'required',
        'city' => 'required',
        'province' => 'required',
        'postalcode' => 'required'
    ];

    protected function messages()
    {
        return [
            '*.required' => "this field is required",
            'email.email' => "email format is wrong",
        ];
    }

    public function mount($userId)
    {

        $user = User::find($userId);

        $this->userId = $userId;
        $this->name = $user->name;
        $this->surname = $user->surname;
        $this->email = $user->email;
        $this->address = optional($user->address)->address ?? "";
        $this->city = optional($user->address)->city ?? "";
        $this->province = optional($user->address)->province ?? "";
        $this->postalcode = optional($user->address)->postalcode ?? "";
    }

    // alternative
// all the public attribute + $user
    // public function mount(User $user)
    // {
    //     $this->user = $user;  take the istance of User with the passed id form url
    //     $this->id = $user->id;
    //     $this->name = $user->name;
    //     .......
    // }

    public function update()
    {

        $this->validate();

        try {

            $user = User::find($this->userId);

            $user->name = $this->name;
            $user->surname = $this->surname;
            $user->email = $this->email;


            $existingAddress = Address::where([
                'address' => $this->address,
                'city' => $this->city,
                'province' => $this->province,
                'postalcode' => $this->postalcode,
            ])->first();

            if (!$existingAddress) {

                $address = Address::create([

                    'address' => $this->address,
                    'city' => $this->city,
                    'province' => $this->province,
                    'postalcode' => $this->postalcode,
                ]);

            } else {

                $address = $existingAddress;

            }


            $address->save();
            $user->address()->associate($address); // alternative method not to use the foreign key.

            $user->save();
            session()->flash('success', 'Customer updated sucessfully!');

        } catch (\Exception $e) {

            session()->flash('error', 'Error updating customer: ' . $e->getMessage());

        }

        $this->redirect("/user/$this->userId", navigate: true);
    }

    public function delete()
    {
        try {

            $user = User::find($this->userId);

            if ($user) {

                $user->delete();
                session()->flash('success', 'Customer deleted successfully!');
                return redirect("/data");

            } else {

                session()->flash('error', 'Customer not found');


            }

        } catch (\Exception $e) {

            session()->flash('error', 'Error deleting customer: ' . $e->getMessage());

            return redirect()->back();

        }


    }


    public function render()
    {
        return view('livewire.update-user');
    }
}
