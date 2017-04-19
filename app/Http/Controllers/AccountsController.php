<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Requests;
use App\Models\Account;
use Carbon\Carbon;
use App\Models\Schedule;

class AccountsController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @author Andrew Haswell
   */
  public function index()
  {

    //$id = Auth::id();
    //$user = Auth::user();

    if (!Auth::check()) {
      echo 'Not logged in';
    }

    // Get our accounts
    $accounts = Account::orderBy('type', 'asc')->orderBy('name', 'asc')->get();

    $title = 'Accounts';

    // Update the balance based on current balance and transaction since that point
    foreach ($accounts as &$account) {
      $account = $this->get_current_balance($account, false);
    }

    // Show the accounts
    return view('accounts.test', compact(['accounts',
                                          'title']));
  }

  /**
   * @param $id
   *
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
   * @author Andrew Haswell
   */
  public function detail($id)
  {
    $account = Account::findOrFail($id);
    $future_account = clone $account;
    $title = 'Accounts - ' . $account->name;
    $account = $this->get_current_balance($account, false);
    $transactions = $account->transactions()->orderBy('payment_date', 'desc')->orderBy('type', 'desc')->get();
    $end_of_month = Carbon::parse('last day of this month');
    $schedules = $account->schedules()->where('payment_date', '<=', $end_of_month)->where('payment_date', '>', Carbon::parse('today'))->orderBy('payment_date', 'desc')->orderBy('type', 'desc')->get();
    $future_balance = $this->get_future_balance($future_account, $end_of_month)->balance;

    return view('accounts.account', compact(['account',
                                             'schedules',
                                             'transactions',
                                             'title',
                                             'end_of_month',
                                             'future_balance']));
  }

  public function future($id, $month)
  {
    $dt = Carbon::createFromFormat('!m', $month);
    $month = $dt->format('F');
    $date = new Carbon('last day of ' . $month);

    $account = Account::findOrFail($id);
    $account = $this->get_future_balance($account, $date);
    $transactions = $account->transactions()->orderBy('payment_date', 'desc')->orderBy('type', 'desc')->get();
    $schedules = $account->schedules()->where('payment_date', '<=', $date)->where('payment_date', '>', Carbon::parse('today'))->orderBy('payment_date', 'desc')->orderBy('type', 'desc')->get();
    return view('accounts.account_future', compact('account', 'transactions', 'schedules'));
  }

  /**
   * @author Andrew Haswell
   */

  public function balance()
  {
    $this->get_accounts();
  }

  /**
   * @author Andrew Haswell
   */

  public function get_accounts()
  {
    $accounts = Account::all();
    foreach ($accounts as $account) {
      $account = $this->get_current_balance($account, false);
      dump($account);
    }
  }

  /**
   * @param $account
   *
   * @return mixed
   * @author Andrew Haswell
   */

  public function get_current_balance($account, $confirmed_only = true)
  {
    $transactions = $account->transactions()->where('payment_date', '>=', $account->balance_date)->get();
    foreach ($transactions as $transaction) {
      // If we're only checking for confirmed transactions and it's not, skip it
      if ($confirmed_only && !$transaction->confirmed) {
        continue;
      }
      if ($transaction->type == 'debit') {
        $account->balance -= $transaction->amount;
      } else {
        $account->balance += $transaction->amount;
      }
    }
    return $account;
  }

  /**
   * @param $account
   * @param $date
   *
   * @return mixed
   * @author Andrew Haswell
   */

  public function get_future_balance($account, $date)
  {
    $transactions = $account->transactions()->where('payment_date', '>=', $account->balance_date)->get();
    foreach ($transactions as $transaction) {
      if ($transaction->type == 'debit') {
        $account->balance -= $transaction->amount;
      } else {
        $account->balance += $transaction->amount;
      }
    }

    //dump($account);

    $schedules = $account->schedules()->where('payment_date', '<=', $date)->where('payment_date', '>', Carbon::parse('today'))->orderBy('payment_date', 'asc')->get();

    foreach ($schedules as $schedule) {

      //dump($schedule);

      if ($schedule->type == 'debit') {
        $account->balance -= $schedule->amount;
      } else {
        $account->balance += $schedule->amount;
      }
      //dump($account->balance);
    }

    // dd($account);

    return $account;
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   *
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  int $id
   *
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int $id
   *
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  int                      $id
   *
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int $id
   *
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    //
  }
}
