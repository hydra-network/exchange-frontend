<div class="sidebar" id="sidebar" data-color="#000">
    <!--div class="logo">

    </div-->

    <div class="logo logo-mini">

    </div>

    <div class="sidebar-wrapper">

        <ul class="nav">
            <li>
                <a href="{{ route('app.dashboard') }}">
                    <i class="glyphicon glyphicon-align-justify"></i>
                    <p>Markets</p>
                </a>
            </li>

            <li>
                <a href="{{ route('app.escrow.index') }}">
                    <i class="glyphicon glyphicon-retweet"></i>
                    <p>Escrow</p>
                </a>
            </li>

            <li>
                <a href="{{ route('app.balances') }}">
                    <i class="glyphicon glyphicon-bitcoin"></i>
                    <p>@lang('dictionary.balances')</p>
                </a>
            </li>

            <li>
                <a href="{{ route('app.history') }}">
                    <i class="glyphicon glyphicon-time"></i>
                    <p>History</p>
                </a>
            </li>
            
            <li style="margin-top: 100px;">
                <a href="{{ route('app.profile') }}">
                    <i class="fa fa-user"></i>
                    <p>Profile</p>
                </a>
            </li>

            <li>
                <a href="{{ route('logout') }}" onclick="if (!confirm('Logout?')) return false;">
                    <i class="fa fa-sign-out"></i>
                    <p>@lang('dictionary.logout')</p>
                </a>
            </li>
        </ul>
    </div>
</div>
