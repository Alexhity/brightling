{{-- resources/views/auth/admin/reviews/index.blade.php --}}
@extends('layouts.app')

@section('styles')
    <style>
        .admin-content-wrapper {
            margin-left: 200px;
            width: calc(100% - 200px);
            font-family: 'Montserrat Medium', sans-serif;
        }
        .admin-content-wrapper h2 {
            font-family: 'Montserrat Bold', sans-serif;
            color: #333333;
            font-size: 32px;
            margin-top: 30px;
            margin-bottom: 30px;
            text-align: left;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-create {
            display: inline-block;
            padding: 10px 16px;
            background-color: #e6e2f8;
            color: black;
            text-decoration: none;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn-create:hover {
            background-color: #c4b6f3;
        }
        table.reviews {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-size: 16px;
        }
        .reviews th,
        .reviews td {
            padding: 12px 20px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .reviews th {
            background: #fff6d0;
            font-family: 'Montserrat SemiBold', sans-serif;
            color: #333333;
            font-size: 16px;
        }
        .reviews td {
            font-size: 14px;
        }
        .table-action-btn {
            display: inline-block;
            width: 100px;
            padding: 6px 0;
            text-align: center;
            font-family: 'Montserrat Medium', sans-serif;
            font-size: 14px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            color: black;
        }
        .btn-approve {
            background: #d4edda;
        }
        .btn-approve:hover {
            background: #a8ddb5;
        }
        .btn-reject {
            background: #ffe6e6;
        }
        .btn-reject:hover {
            background: #ffb3b3;
        }
        .btn-delete {
            background: #ffcccc;
        }
        .btn-delete:hover {
            background: #ff9999;
        }
        .badge-pending {
            background: #fff3cd;
            color: #856404;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
        }
        .badge-approved {
            background: #d4edda;
            color: #155724;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
        }
        .badge-rejected {
            background: #f8d7da;
            color: #721c24;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
        }

        .btn-view {
            background: #e6f7ff;
        }
        .btn-view:hover {
            background: #b3e5ff;
        }
    </style>
@endsection

@section('content')
    @include('layouts.left_sidebar_admin')

    <div class="admin-content-wrapper">
        <h2>Модерация отзывов</h2>

        <table class="reviews">
            <thead>
            <tr>
                <th></th>
                <th>Автор</th>
                <th>Курс</th>
                <th>Заголовок</th>
                <th>Статус</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @forelse($reviews as $review)
                <tr>
                    {{-- Кнопка «+» --}}
                    <td>
                        <button class="btn-expand"
                                data-title="{{ $review->title }}"
                                data-comment="{{ e($review->comment ?: '—') }}"
                                data-rating="{{ $review->rating }}"
                                data-course="{{ e(optional($review->course)->title ?: '—') }}"
                                style="background:none;border:none;cursor:pointer;font-size:18px;">
                            +
                        </button>
                    </td>
                    <td>{{ $review->user->first_name }} {{ $review->user->last_name }}</td>
                    <td>{{ optional($review->course)->title ?? '—' }}</td>
                    <td>{{ Str::limit($review->title, 30) }}</td>
                    <td>
                        @if($review->status === 'pending')
                            <span class="badge-pending">На модерации</span>
                        @elseif($review->status === 'approved')
                            <span class="badge-approved">Одобрен</span>
                        @else
                            <span class="badge-rejected">Отклонён</span>
                        @endif
                    </td>
                    <td>{{ $review->created_at->format('d.m.Y') }}</td>
                    <td>

                        @if($review->status === 'pending')
                            {{-- Одобрить --}}
                            <form action="{{ route('admin.reviews.updateStatus', $review) }}"
                                  method="POST"
                                  class="inline-block">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="table-action-btn btn-approve">Одобрить</button>
                            </form>
                            {{-- Отклонить --}}
                            <form action="{{ route('admin.reviews.updateStatus', $review) }}"
                                  method="POST"
                                  class="inline-block">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="table-action-btn btn-reject">Отклонить</button>
                            </form>
                        @endif

                        {{-- Удалить --}}
                        <form action="{{ route('admin.reviews.destroy', $review) }}"
                              method="POST"
                              data-delete-form
                              data-review-title="{{ $review->title }}"
                              class="inline-block">
                            @csrf @method('DELETE')
                            <button type="submit" class="table-action-btn btn-delete">Удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 20px; font-style: italic; color: #666;">
                        Нет отзывов для модерации
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // toast при success
            @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            @endif

            // подтверждение удаления
            document.querySelectorAll('form[data-delete-form]').forEach(form => {
                form.addEventListener('submit', e => {
                    e.preventDefault();
                    const title = form.dataset.reviewTitle;
                    Swal.fire({
                        title: `Удалить отзыв «${title}»?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Да, удалить',
                        cancelButtonText: 'Отмена',
                        reverseButtons: true,
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });


            document.querySelectorAll('.btn-expand').forEach(btn => {
                btn.addEventListener('click', () => {
                    Swal.fire({
                        title: btn.dataset.title,
                        html: `
                    <p><strong>Курс:</strong> ${btn.dataset.course}</p>
                    <p><strong>Оценка:</strong> ${btn.dataset.rating} / 5</p>
                    <hr>
                    <p>${btn.dataset.comment.replace(/\n/g, '<br>')}</p>
                `,
                        width: 600,
                        confirmButtonText: 'Закрыть'
                    });
                });
            });
        });
    </script>
@endsection
