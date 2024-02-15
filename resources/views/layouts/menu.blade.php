<li class="nav-item">
    <a href="{{ route('dashboard') }}" class="nav-link {{ Request::is('dashboard*') ? 'active' : '' }}">
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-divider"><hr class="border-bottom border-1 border-white"></li> <!-- Divider -->

<li class="nav-item">
    <a href="{{ route('retrieve-new-items') }}" class="nav-link {{ Request::is('retrieve-new*') ? 'active' : '' }}">
        <p>Retrieve New Items</p>
    </a>
</li>


<li class="nav-item">
    <a href="#" class="nav-link">
        <p>Process Items</p>
    </a>
</li>

<li class="nav-item">
    <a href="#" class="nav-link">
        <p>Reports</p>
    </a>
</li>

<li class="nav-divider"><hr class="border-bottom border-1 border-white"></li> <!-- Divider -->

<li class="nav-item">
    <a href="{{ route('items.index') }}" class="nav-link {{ Request::is('items*') ? 'active' : '' }}">
        <p>Edit Items</p>
    </a>
</li>
