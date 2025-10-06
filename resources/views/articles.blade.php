@extends('include.app')

@section('content')
    <div class="card mt-3">
        <div class="card-header d-flex align-items-center">
            <h4 class="mb-0">{{ __('Articles') }}</h4>
            <button data-toggle="modal" data-target="#addArticleModal"
                class="ml-auto btn btn-primary text-white rounded-pill">{{ __('Add Article') }}</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Interests') }}</th>
                            <th>{{ __('Published Date') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->title }}</td>
                                <td>
                                    <img src="{{ asset('storage/' . $item->image) }}" width="50" height="50"
                                        class="rounded">
                                </td>
                                <td>
                                    @foreach ($item->interests as $interest)
                                        <span class="badge badge-info">{{ $interest->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $item->created_at->diffForHumans() }}</td>
                                <td>
                                    <button type="button" data-toggle="modal" data-target="#editModal{{ $item->id }}"
                                        class="btn btn-sm btn-primary rounded-pill">
                                        {{ __('Edit') }}
                                    </button>
                                    <form action="{{ route('articles.delete', $item->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger rounded-pill" type="submit"
                                            onclick="return confirm('Are you sure you want to delete this article?')">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Article Modal for each item --}}
                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5>{{ __('Edit Article') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('articles.update', $item->id) }}" method="post"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group">
                                                    <label>{{ __('Title') }}</label>
                                                    <input type="text" name="title" class="form-control"
                                                        value="{{ $item->title }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('Image') }}</label>
                                                    <input type="file" name="image" class="form-control-file">
                                                    <small class="form-text text-muted">Leave empty to keep current
                                                        image</small>
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $item->image) }}" width="100"
                                                            class="rounded">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('Content') }}</label>
                                                    <textarea name="content" class="form-control" rows="5" required>{{ $item->content }}</textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>{{ __('Interests') }}</label>
                                                    <select name="interests[]" class="form-control" multiple required
                                                        size="5">
                                                        @foreach (App\Models\Interests::all() as $interest)
                                                            <option value="{{ $interest->id }}"
                                                                {{ $item->interests->contains($interest->id) ? 'selected' : '' }}>
                                                                {{ $interest->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="form-text text-muted">Hold Ctrl/Cmd to select
                                                        multiple</small>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <button class="btn btn-primary"
                                                        type="submit">{{ __('Update') }}</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">{{ __('Cancel') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center mt-3">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Add Article Modal --}}
    <div class="modal fade" id="addArticleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>{{ __('Add Article') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('articles.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>{{ __('Title') }}</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Image') }}</label>
                            <input type="file" name="image" class="form-control-file" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Content') }}</label>
                            <textarea name="content" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Interests') }}</label>
                            <select name="interests[]" class="form-control" multiple required size="5">
                                @foreach (App\Models\Interests::all() as $interest)
                                    <option value="{{ $interest->id }}">{{ $interest->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple</small>
                        </div>
                        <div class="form-group mb-0">
                            <button class="btn btn-primary" type="submit">{{ __('Submit') }}</button>
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
