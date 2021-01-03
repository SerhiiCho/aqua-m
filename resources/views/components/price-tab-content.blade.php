<div class="row">
    <div class="col s12">
        <ul class="collapsible">
            @foreach ($categories as $category => $items)
                <li class="{{ $loop->index === 0 ? 'active' : '' }}">
                    <div class="collapsible-header">
                        <i class="material-icons teal-text text-darken-2">arrow_drop_down</i>
                        <b class="teal-text text-darken-2" style="margin-right:5px">{{ count($items) }}</b>
                        {{ $category }}
                    </div>

                    <div class="collapsible-body">
                        <table class="striped responsive-table">
                            <thead>
                            <tr>
                                @foreach ($names as $name)
                                    <th>{{ $name }}</th>
                                @endforeach
                                <th>Изображение</th>
                            </tr>
                            </thead>
                            <tbody class="striped">
                                @foreach ($items as $item)
                                    <tr>
                                        @foreach ($keys as $key)
                                            <td>{{ $item[$key] }}</td>
                                        @endforeach

                                        <td>
                                            <div data-width="120"
                                                 class="async-load spinner"
                                                 data-async-load="{{ $item['image'] ?? '' }}"
                                                 data-class="z-depth-1"
                                            ></div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
