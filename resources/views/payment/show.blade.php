@extends('layouts.app')
@section('title', 'Account | Payments')
@section('meta')
<style>
    th {
        font-size: 12px;
        text-transform: uppercase;
        color: #888 !important;
    }
</style>
@endsection
@section('back')
<a href="{{ route('profile.view') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">
      <h1 class="lh-title mb-3">Payments</h1>

      <table class="table">
        <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Package</th>
              <th scope="col">Amount</th>
              <th scope="col">Method</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($payment as $item)
            <tr>
              <th scope="row">1</th>
              <td>{{ $item->userPackage->package->name }}</td>
              <td>${{ $item->amount }}</td>
              <td>{{ $item->method }}</td>
              <td><span style="margin-bottom: 12px;background: var(--green); border-radius: 6px;border: 2px solid var(--green-dark)" class="badge">{{ $item->status }}</span></td>
            </tr>
            @endforeach
          </tbody>
      </table>
</div>
@endsection
@section('script')
