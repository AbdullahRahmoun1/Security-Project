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
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'balance' => $user->balance,
        ]);
    }

    public function index()
    {
        $users = User::all();
        // dd($users);
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
        }else {
            return response()->json(['success' => false, 'message' => 'Unknown Operation.'], 400);
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
    $perPage = $validated['per_page'] ?? 3;
    $user = User::findOrFail($id);
    $transactions = $user->transactions()->paginate($perPage);

    return response()->json([
        'success' => true,
        'message' => 'Success.',
        'data' => [
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
            'transactions' => $transactions->items(),
        ],
    ]);
}

}
