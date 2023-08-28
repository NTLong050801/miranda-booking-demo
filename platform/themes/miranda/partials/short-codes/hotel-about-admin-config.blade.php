<div class="form-group">
    <label class="control-label">{{ __('Title') }}</label>
    <input type="text" name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" placeholder="{{ __('Title') }}">
</div>

<div class="form-group">
    <label class="control-label">{{ __('Subtitle') }}</label>
    <textarea name="subtitle" class="form-control" placeholder="{{ __('Subtitle') }}" rows="3">{{ Arr::get($attributes, 'subtitle') }}</textarea>
</div>

@for($i = 1; $i <= 5; $i++)
    <div class="form-group">
        <label class="control-label">{{ __('Block icon :number', ['number' => $i]) }}</label>
        {!! Form::themeIcon('block_icon_' . $i, Arr::get($attributes, 'block_icon_' . $i)) !!}
    </div>

    <div class="form-group">
        <label class="control-label">{{ __('Block text :number', ['number' => $i]) }}</label>
        <input type="text" name="block_text_{{ $i }}" value="{{ Arr::get($attributes, 'block_text_' . $i) }}" class="form-control"
               placeholder="{{ __('Block text :number', ['number' => $i]) }}">
    </div>

    <div class="form-group">
        <label class="control-label">{{ __('Block link :number', ['number' => $i]) }}</label>
        <input type="text" name="block_link_{{ $i }}" value="{{ Arr::get($attributes, 'block_link_' . $i) }}" class="form-control"
               placeholder="{{ __('Block link :number', ['number' => $i]) }}">
    </div>
@endfor
