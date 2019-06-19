@extends('../../layouts.app')

@section('top')
    @parent
@endsection

@section('heading')
    <i class="fas fa-clipboard-check titleIcon"></i>STATUS
@endsection

@section('content')
    <div class="contentContainer">

        <div>
            <div class="row listFilter">
                <form>
                    <label for="fromDate">From</label>
                    <input type="date" name="fromDate" placeholder="DDMMYY">
                    <label for="toDate">To</label>
                    <input type="date" name="toDate" placeholder="DDMMYY">
                    <select name="status">
                        <option style="display: none;" disabled="" selected="">Status</option>
                        <option>Encoding</option>
                        <option>Writing</option>
                        <option>Developing</option>
                    </select>
                    <input type="search" placeholder="Search">
                </form>
            </div>
            <br/>
            <div class="row plistHeader">
                <div class="col">Job ID</div>
                <div class="col">Bags</div>
                <div class="col">Creation Date</div>
                <div class="col">Status</div>
            </div>

            <div>
                <div class="row plist">
                    <div class="col">
                        3a070c3d-10b1-4188-b877-4c7a0d730b05 
                    </div>
                    <div class="col">
                        1
                    </div>
                    <div class="col">
                        {{ $bag->created_at }}
                    </div>
                    <div class="col">
                        Encoding
                    </div>
                </div>
            </div>
        
        </div>
    
    </div>
@endsection
