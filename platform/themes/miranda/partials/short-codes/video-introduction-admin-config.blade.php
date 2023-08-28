<div class="form-group">
    <label class="control-label">{{ __('Title') }}</label>
    <input type="text" name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" placeholder="{{ __('Title') }}">
</div>

<div class="form-group">
    <label class="control-label">{{ __('Subtitle') }}</label>
    <textarea name="subtitle" class="form-control" placeholder="{{ __('Subtitle') }}" rows="3">{{ Arr::get($attributes, 'subtitle') }}</textarea>
</div>

<div class="form-group">
    <label class="control-label">{{ __('Content') }}</label>
    <textarea name="content" class="form-control" placeholder="Content"
              rows="3">{{ Arr::get($attributes, 'content') }}</textarea>
</div>

<div class="form-group">
    <label class="control-label">{{ __('Background Image') }}</label>
    {!! Form::mediaImage('background_image', Arr::get($attributes, 'background_image')) !!}
</div>

<div class="form-group">
    <label class="control-label">{{ __('Video Image') }}</label>
    {!! Form::mediaImage('video_image', Arr::get($attributes, 'video_image')) !!}
</div>

<div class="form-group">
    <label class="control-label">{{ __('Video URL') }}</label>
    <input type="text" name="video_url" value="{{ Arr::get($attributes, 'video_url') }}" class="form-control"
           placeholder="{{ __('Video URL') }}">
</div>

<div class="form-group">
    <label class="control-label">{{ __('Button text') }}</label>
    <input type="text" name="button_text" value="{{ Arr::get($attributes, 'button_text') }}" class="form-control"
           placeholder="{{ __('Button text') }}">
</div>

<div class="form-group">
    <label class="control-label">{{ __('Button URL') }}</label>
    <input type="text" name="button_url" value="{{ Arr::get($attributes, 'button_url') }}" class="form-control"
           placeholder="{{ __('Button URL') }}">
</div>
