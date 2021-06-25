<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contacts = Contact::where(['created_by' => Auth::user()->id])->get();

        return response()->json(
            [
                'contacts' => $contacts,
            ],
            200
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $contact = Contact::create(
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'bio' => $request->bio,
                'created_by' => Auth::user()->id
            ]
        );

        return response()->json(
            [
                'contact' => $contact,
                'message' => 'Contact have been created successfully!'
            ],
            200
        );
    }


    /**
     * Display a details of the particularresource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $contacts = Contact::where(
            [
                'created_by' => Auth::user()->id,
                'id' => $id
            ]
        )->get();

        return response()->json($contacts, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $contact = Contact::find($id);
        $contact->name = $request->name;
        $contact->phone = $request->phone;
        $contact->email = $request->email;
        $contact->address = $request->address;
        $contact->bio = $request->bio;
        $contact->save();

        return response()->json(
            [
                'contact' => $contact,
                'message' => 'Contact have been updated successfully!'
            ],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(
            [
                'message' => 'Contact have been deleted successfully!'
            ],
            200
        );
    }
}
