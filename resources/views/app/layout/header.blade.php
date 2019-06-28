<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!--div class="navbar-minimize">
            <button id="minimizeSidebar" class="btn btn-default btn-fill btn-round btn-icon">
                <i class="glyphicon glyphicon-align-justify"></i>
            </button>
        </div-->

        <div class="navbar-header">
            <!--button type="button" class="navbar-toggle" data-toggle="collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button-->

            <h1 style="padding-left: 14px;">@yield('title')</h1>
        </div>

        <div class="collapse navbar-collapse">
            <div class="text-right" style="padding-top: 3px">
                @stack('page-actions')
            </div>
        </div>
    </div>
</nav>