<?php

namespace App\Services;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media
{
	protected $fileName;
	protected $default;
	protected $storePath;

	public function __construct(
		null|string $fileName = null,
		null|string $default = null,
		string|array $storePath = '',
	) {
		$this->fileName = $fileName ? Str::afterLast($fileName, '/') : '';
		$this->default = $default;
		$this->storePath = $this->path($storePath);
	}

	public function url(mixed $key = null): string|null
	{
		$media = null;
		$path = !$key
			? reset($this->storePath)
			: ($this->storePath[$key] ?? public_path('uploads/'));
		$data = $this->attr($path);
		
		if ($this->fileName && $key != 'default' && $data['exist'])
		{
			$media = $data['url'];
		}
		else
		{
			$media = $this->default;
		}

		return $media;
	}

	public function store(
		null|UploadedFile $file,
		null|string $as = null,
		null|Closure $closure = null
	): string
	{
		$fileName = $this->fileName;
		
		if ($file)
		{
			$this->delete();
			$fileName = ($as ?? Str::random(16)) . '.' . $file->extension();

			foreach ($this->storePath as $key => $path)
			{
				$cloneFile = $file;

				if (count($this->storePath) > 1)
				{
					$temp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $file->getClientOriginalName();
					File::copy($file->getRealPath(), $temp);
					$cloneFile = new UploadedFile(
						$temp,
						$file->getClientOriginalName(),
						$file->getClientMimeType(),
						null,
						true
					);
				}

				if (!File::exists($path))
				{
					File::makeDirectory($path, 0755, true);
				}

				if ($closure)
				{
					$closure($cloneFile, $fileName, $path, $key);
				}
				else
				{
					$cloneFile->move($path, $fileName);
				}

				if (isset($temp) && File::exists($temp))
				{
					File::delete($temp);
				}
			}
		}

		return $fileName;
	}

	public function delete(): void
	{
		foreach ($this->storePath as $path)
		{
			if (File::exists($path . $this->fileName))
			{
				File::delete($path . $this->fileName);
			}
		}
	}

	protected function path(string|array $path): array
	{
		if (!$path)
		{
			$data = [public_path('uploads/')];
		}
		else
		{
			foreach ((!is_array($path) ? [$path] : $path) as $key => $value)
			{
				$data[$key] = Str::endsWith($value, '/') ? $value : $value . '/';
			}
		}

		return $data;
	}

	protected function attr(string $path = ''): array
	{
		$data = [];

		foreach (($path ? [$path] : $this->storePath) as $key => $value)
		{
			$data[$key] = [
				'path' => $value . $this->fileName,
				'type' => null,
				'url' => null,
				'exist' => false,
			];
	
			if (Str::startsWith($value, public_path()))
			{
				$url = Str::replace(public_path(), '',$value);
				$data[$key]['type'] = 'public';
				$data[$key]['url'] = asset($url . $this->fileName);
				$data[$key]['exist'] = File::exists($data[$key]['path']);
			}
			else if (Str::startsWith($value, storage_path()))
			{
				$url = Str::replace(storage_path(), '',$value);
				$data[$key]['type'] = 'storage';
				$data[$key]['url'] = Storage::url($url . $this->fileName);
				$data[$key]['exist'] = Storage::exists($data[$key]['path']);
			}
		}

		return count($data) > 1 ? $data : array_merge(...$data);
	}

	public function __get($key): mixed
	{
		$attributes = [
			'name' => $this->fileName ?: null,
			'store' => $this->attr()
		];

		if (isset($attributes[$key]))
		{
			if (is_array($attributes[$key]))
			{
				return new class($attributes[$key]) {
					private array $attributes;
		
					public function __construct(array $attributes)
					{
						$this->attributes = $attributes;
					}
		
					public function __get($key): mixed
					{
						if (isset($this->attributes[$key]))
						{
							return is_array($this->attributes[$key])
								? (new self($this->attributes[$key]))
								: $this->attributes[$key];
						}
		
						return null;
					}
				};
			}

			return $attributes[$key];
		}

		return null;
	}
}