<!-- resources/views/text-items/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Управление таблицами</h3>
                </div>
                <div class="card-body">
                    <!-- Кнопки действий -->
                    <div class="mb-3">
                        <form action="{{ route('text-items.generate-random') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">Сгенерировать 1000 случайных элементов</button>
                        </form>
                        
                        <form action="{{ route('text-items.clear-all') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Вы уверены?')">Очистить все элементы</button>
                        </form>
                        
                        <a href="{{ route('fetch.data') }}" target="_blank" class="btn btn-info">Получить данные из Google Таблицы</a>
                    </div>

                    <!-- Форма для Google Sheet URL -->
                    <div class="mb-4">
                        <h5>Настройка Google Таблицы</h5>
                        <form action="{{ route('text-items.update-google-sheet-url') }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="url" name="google_sheet_url" class="form-control" 
                                       value="{{ $googleSheetUrl }}" placeholder="Введите URL Google Таблицы" required>
                                <button type="submit" class="btn btn-success">Обновить URL</button>
                            </div>
                        </form>
                        @if($googleSheetUrl)
                            <small class="form-text text-muted">
                                Текущий URL: <a href="{{ $googleSheetUrl }}" target="_blank">{{ $googleSheetUrl }}</a>
                            </small>
                        @endif
                    </div>

                    <!-- Форма создания -->
                    <div class="mb-4">
                        <h5>Создать новый элемент</h5>
                        <form action="{{ route('text-items.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" name="title" class="form-control" placeholder="Заголовок" required>
                                </div>
                                <div class="col-md-4">
                                    <textarea name="content" class="form-control" placeholder="Содержание" required></textarea>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-control" required>
                                        <option value="Allowed">Разрешено</option>
                                        <option value="Prohibited">Запрещено</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success">Создать</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Таблица с данными -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Заголовок</th>
                                    <th>Содержание</th>
                                    <th>Статус</th>
                                    <th>Создан</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ Str::limit($item->content, 50) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->status === 'Allowed' ? 'success' : 'danger' }}">
                                            {{ $item->status === 'Allowed' ? 'Разрешено' : 'Запрещено' }}
                                        </span>
                                    </td>
                                    <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <form action="{{ route('text-items.destroy', $item->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены?')">Удалить</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection