<div class="form-group">
    <label class="control-label">{{ __('YouTube URL') }}</label>
    <input name="url" value="{{ Arr::get($attributes, 'url') }}" class="form-control"
           placeholder="https://www.youtube.com/watch?v=FN7ALfpGxiI">
</div>

<div class="form-group">
    <label class="control-label">{{ __('Background Image') }}</label>
    {!! Form::mediaImage('background_image', Arr::get($attributes, 'background_image')) !!}
</div>
