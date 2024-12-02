<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
     public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'balance' => $user->balance,
            'public_key' => $user->public_key,
        ]);
    }

    public function index()
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'data' => $users,
        ], 200);
    }

    public function updateBalance(Request $request, $id)
{
    $validated = $request->validate([
        'amount' => 'required|numeric|min:0.01',
        'operation' => 'required|in:add,subtract',
    ]);

    $user = User::findOrFail($id);
    
    if ($validated['operation'] === 'add') {
        $user->balance += $validated['amount'];
        $transactionType = 'deposit';
    } elseif ($validated['operation'] === 'subtract') {
        if ($user->balance < $validated['amount']) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance for withdrawal.'], 400);
        }
        $user->balance -= $validated['amount'];
        $transactionType = 'withdraw';
    }
    $user->save();
    $user->transactions()->create([
        'type' => $transactionType,
        'amount' => $validated['amount'],
    ]);

    return response()->json([
        'success' => true,
        'message' => 'The balance has been updated successfully.',
        'data' => $user,
    ], 200);
}

public function getTransactions(Request $request, $id)
{
    $validated = $request->validate([
        'per_page' => 'sometimes|integer|min:1|max:50', 
    ]);
    $perPage = $validated['per_page'] ?? 5; 
    $user = User::findOrFail($id);
    $transactions = $user->transactions()->simplePaginate($perPage);
    return response()->json([
        'success' => true,
        'message' => 'Success.',
        'data' => $transactions,
    ]);
}
}
