<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Notes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('customer-list');

        if (Auth::user()->isAdmin()) {
            // Authenticated user is an admin, show all customers
            $customers = Customer::all();
        } else {
            // Authenticated user is not an admin, show only their own customers
            $customers = Customer::where('created_by', Auth::id())->get();
        }

        return response()->json(['data' => $customers], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('add-customer');

        $validatedData = $this->validate($request, [
            'companyName' => 'required|string',
            'contactNo' => 'required|unique:customers|regex:/^[0-9]+$/',
        ]);

        $validatedData['created_by'] = Auth::id();

        $validatedData['email'] = $request->input('email');
        $validatedData['keyPerson'] = $request->input('keyPerson');
        $validatedData['address'] = $request->input('address');
        $validatedData['keyPerson'] = $request->input('keyPerson');
        $validatedData['customerType'] = $request->input('customerType');
        $validatedData['productType'] = $request->input('productType');
        $validatedData['suggestedModel'] = $request->input('suggestedModel');
        $validatedData['purchasePlan'] = $request->input('purchasePlan');
        $validatedData['date'] = $request->input('date');
        $validatedData['token'] = $request->input('token');
        $validatedData['reference'] = $request->input('reference');

        $customer = Customer::create($validatedData);

        return response()->json(['message' => 'Customer created successfully', 'data' => $customer], 201);
    }




    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $this->authorize('edit-customer');

        return response()->json($customer);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('edit-customer');

        $validatedData = $this->validate($request, [
            'companyName' => 'required|string',
            'contactNo' => 'required|regex:/^[0-9]+$/|unique:customers,contactNo,' . $id,
        ]);

        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->companyName = $validatedData['companyName'];
        $customer->contactNo = $validatedData['contactNo'];
        $customer->email = $request->input('email');
        $customer->keyPerson = $request->input('keyPerson');
        $customer->address = $request->input('address');
        $customer->customerType = $request->input('customerType');
        $customer->productType = $request->input('productType');
        $customer->suggestedModel = $request->input('suggestedModel');
        $customer->purchasePlan = $request->input('purchasePlan');
        $customer->date = $request->input('date');
        $customer->reference = $request->input('reference');
        $customer->token = $request->input('token');

        $customer->save();

        return response()->json(['message' => 'Customer updated successfully', 'data' => $customer], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete-customer');

        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully'], 200);
    }

    public function createNote(Request $request)
    {
        $data = $request->all();

        $notes = new Notes();
        $notes->customer_id = $data['customer_id'];
        $notes->date = date('Y-m-d H:i:s', strtotime($data['followUpDate']));
        $notes->note = $data['note'];
        $notes->created_by = Auth::id();
        $notes->save();

        return response()->json(['message' => 'Notes created successfully'], 200);
    }

    public function listNotes($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        if (Auth::user()->isAdmin()) {
            $notes = $customer->notes->load('createdByUser');
        } else {
            $notes = $customer->notes()->where('created_by', Auth::id())->get();
        }

        $formattedNotes = $notes->map(function ($note) {
            return [
                'id' => $note->id,
                'notes' => $note->note,
                'created_by' => [
                    'id' => $note->createdByUser->id,
                    'name' => $note->createdByUser->name,
                ],
                'created_at' => $note->created_at->toDateTimeString(),
            ];
        });

        return response()->json(['notes' => $formattedNotes], 200);
    }
}
