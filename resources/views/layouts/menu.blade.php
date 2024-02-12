<li class="nav-item">
    <a href="{{ route('items.index') }}" class="nav-link {{ Request::is('items*') ? 'active' : '' }}">
        <p>Items</p>
    </a>
</li>

<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <p>Processes<i class="right fas fa-angle-left"></i></p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('operations.retrieve_items') }}" class="nav-link">
                <p>Retrieve New Items</p>
            </a>
        </li>
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('show_clean_up') }}" class="nav-link">--}}
{{--                <p>Clean Up</p>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('items.update') }}" class="nav-link">--}}
{{--                <p>Update Items</p>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('items.handle_form') }}" class="nav-link">--}}
{{--                <p>Submit Update Items</p>--}}
{{--            </a>--}}
{{--        </li>--}}
{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('items.report') }}" class="nav-link">--}}
{{--                <p>Generate Report</p>--}}
{{--            </a>--}}
{{--        </li>--}}
    </ul>
</li>
