<ul {!! $options !!}>
    @php $menu_nodes->loadMissing('metadata'); @endphp
    @foreach ($menu_nodes as $key => $row)
        <li class="@if ($row->css_class) {{ $row->css_class }} @endif @if ($row->active) active @endif">
            <a href="{{ url($row->url) }}" @if ($row->target !== 'self') target="{{ $row->target }}" @endif>
                @if ($iconImage = $row->getMetadata('icon_image', true))
                    <img src="{{ RvMedia::getImageUrl($iconImage) }}" alt="icon image" width="12" height="12" style="vertical-align: middle; margin-top: -3px"/>
                @elseif ($row->icon_font) <i class="{{ trim($row->icon_font) }}"></i> @endif {{ $row->title }}
            </a>
            @if ($row->has_child)
                {!! Menu::generateMenu([
                    'menu'       => $menu,
                    'menu_nodes' => $row->child,
                    'options'    => ['class' => 'submenu'],
                    'view'       => 'menu',
                ]) !!}
            @endif
        </li>
    @endforeach
</ul>
