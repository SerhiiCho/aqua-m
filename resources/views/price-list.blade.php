@extends('layouts.app')

@section('title', 'Загрузить прайс лист')

@section('content')

<div class="container" style="margin-top:34px">
    <div class="row">
        <div class="col s12 l6 offset-l3">
            <h5>Обновить прайс лист</h5>
            <p style="padding:10px 0">Выберите прайс лист формата .xls и нажмите "Загрузить"</p>

            <form action="{{ action('PriceListController@store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="file-field input-field">
                    <div class="btn transparent grey-text text-darken-4 waves-effect waves-light">
                        <span>Выбрать файл</span>
                        <input type="file" name="file" required>
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>
                </div>

                <div class="input-field">
                    <button class="btn teal darken-2 waves-effect waves-light" type="submit">
                        <i class="material-icons right">file_upload</i>Загрузить
                    </button>
                </div>
            </form>
        </div>

        @if (count($diff_items) > 0)
            <div class="col s12" style="padding-top:17px">
                <h5>Новые позиции</h5>
                <p>Обратите внимание! Позиции ниже являются новыми. Их не было в прошлом прайс листе.</p>

                <table class="striped responsive-table">
                    <thead>
                        <tr>
                            <th>Номер</th>
                            <th>Название</th>
                            <th>Цена</th>
                            <th>Размер</th>
                            <th>Изображение</th>
                        </tr>
                    </thead>
                    <tbody class="striped">
                        @foreach ($diff_items as $item)
                            <tr>
                                <td>{{ $item->number }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->size }}</td>
                                <td>
                                    <div data-width="120"
                                         class="async-load spinner"
                                         data-async-load="{{ $item->image ?? '' }}"
                                         data-class="z-depth-1"
                                    ></div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
