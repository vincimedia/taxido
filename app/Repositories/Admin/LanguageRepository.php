<?php

namespace App\Repositories\Admin;

use Exception;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Nwidart\Modules\Facades\Module;
use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class LanguageRepository extends BaseRepository
{
    public function model()
    {
        return Language::class;
    }

    public function index($languageTable)
    {
        if (request()->filled('action')) {
            return redirect()->back();
        }

        return view('admin.language.index', ['tableConfig' => $languageTable]);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $this->model->create([
                'name' => $request->name,
                'flag' => $request->flag,
                'locale' => $request->locale,
                'app_locale' => $request->app_locale,
                'is_rtl' => $request->is_rtl,
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('admin.language.index')->with('success', __('static.languages.create_successfully'));
       
        } catch (Exception $e) {
            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {

            $language = $this->model->findOrFail($id);
            $language->update($request);

            DB::commit();
            return to_route('admin.language.index')->with('success', __('static.languages.update_successfully'));
        } catch (Exception $e) {

            DB::rollback();

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {
           
            $language = $this->model->findOrFail($id);
            if($language->system_reserve === 1)
            {
                return to_route('admin.language.index')->with('success', __('static.languages.can_not_delete_default_lang'));
            }

            $language->destroy($id);

            return to_route('admin.language.index')->with('success', __('static.languages.delete_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function status($id, $status)
    {
        try {

            $language = $this->model->findOrFail($id);
            $language->update(['status' => $status]);

            return to_route('admin.language.index')->with('success', __('static.languages.status_update_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function rtl($id, $rtl)
    {
        try {

            $language = $this->model->findOrFail($id);
            $language->update(['rtl' => $rtl]);

            return to_route('admin.language.index')->with('success', __('static.languages.status_update_successfully'));
        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function getLocaleById($id)
    {
        $language = $this->model->findOrFail($id);
        return $language->locale;
    }

    public function translate($request)
    {
        try {
            $locale = $this->getLocaleById($request->id);
            $file = $request->file;
            $dir = resource_path("lang/{$locale}");

            $allFiles = $this->getAllTranslationFiles($locale, $dir);

            if (!$file) {
                $file = head($allFiles);
            }

            $translations = $this->getTranslations($locale, $dir, $file);
            $translations = $this->createPaginate($translations, $request);

            return view('admin.language.translate', [
                'translations' => $translations,
                'allFiles' => $allFiles,
                'file' => $file,
            ]);
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    protected function getAllTranslationFiles($locale, $dir)
    {
        $allFiles = [];

        if (File::isDirectory($dir)) {
            foreach (File::allFiles($dir) as $dirFile) {
                $filename = pathinfo($dirFile, PATHINFO_FILENAME);
                $allFiles[] = $filename;
            }
        }

        $modules = Module::all();
        foreach ($modules as $module) {
            if ($module->isEnabled()) {
                $moduleDir = base_path("Modules/{$module->getName()}/lang/{$locale}");
                if (File::isDirectory($moduleDir)) {
                    foreach (File::allFiles($moduleDir) as $moduleFile) {
                        $filename = pathinfo($moduleFile, PATHINFO_FILENAME);
                        if (!in_array($filename, $allFiles)) {
                            $allFiles[] = $filename;
                        }
                    }
                }
            }
        }

        return $allFiles;
    }

    protected function getTranslations($locale, $dir, $file)
    {
        $translations = [];

        $languageFilePath = "{$dir}/{$file}.php";
        if (file_exists($languageFilePath)) {
            $translations = include $languageFilePath;
        }

        $modules = Module::all();
        foreach ($modules as $module) {
            if ($module->isEnabled()) {
                $moduleDir = base_path("Modules/{$module->getName()}/lang/{$locale}");
                $moduleLanguageFilePath = "{$moduleDir}/{$file}.php";
                if (file_exists($moduleLanguageFilePath)) {
                    $moduleTranslations = include $moduleLanguageFilePath;
                    $translations = array_merge($translations, $moduleTranslations);
                }
            }
        }

        return $translations;
    }

    protected function normalizeTranslations($translations)
    {
        $normalized = [];

        foreach ($translations as $key => $message) {
            if (is_array($message)) {
                $nestedTranslations = $this->normalizeTranslations($message);
                foreach ($nestedTranslations as $nestedKey => $nestedMessage) {
                    $normalized[$nestedKey] = $nestedMessage;
                }
            } else {
                $normalized[$key] = $message;
            }
        }

        return $normalized;
    }

    public function createPaginate($translations, $request)
    {
        $perPage = config('app.paginate', 15);
        $currentPage = $request->input('page', 1);

        $normalizedTranslations = $this->normalizeTranslations($translations);

        $total = count($normalizedTranslations);
        $items = array_slice($normalizedTranslations, ($currentPage - 1) * $perPage, $perPage, true);

        return new LengthAwarePaginator($items, $total, $perPage, $currentPage, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);
    }

    public function translate_update($request, $id)
    {
        try {
            $locale = $this->getLocaleById($id);
            $translations = $request->except('_token');

            $file = $request->file;

            $filePath = resource_path("lang/{$locale}/{$file}.php");

            if (file_exists($filePath)) {
                $existingTranslations = include $filePath;

                foreach ($translations as $key => $value) {
                    $this->updateTranslation($existingTranslations, $key, $value);
                }

                $content = "<?php\n\nreturn " . var_export($existingTranslations, true) . ";\n";
                File::put($filePath, $content); 

                $modules = Module::all();
                foreach ($modules as $module) {
                    if ($module->isEnabled()) {
                        $moduleDir = base_path("Modules/{$module->getName()}/lang/{$locale}");
                        $moduleFilePath = "{$moduleDir}/{$file}.php";

                        if (file_exists($moduleFilePath)) {
                            $moduleTranslations = include $moduleFilePath;

                            foreach ($translations as $key => $value) {
                                $this->updateTranslation($moduleTranslations, $key, $value);
                            }

                            $moduleContent = "<?php\n\nreturn " . var_export($moduleTranslations, true) . ";\n";
                            File::put($moduleFilePath, $moduleContent);  
                        }
                    }
                }

                Artisan::call('cache:clear');

                return to_route('admin.language.index')->with('success', __('static.languages.translate_file_update_successfully'));
            }
            throw new Exception(__('static.languages.file_not_found'), 404);
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }


    public function updateTranslation(&$translations, $key, $value)
    {
        $keys = explode('__', $key);
        $current = &$translations;
        foreach ($keys as $nestedKey) {
            if (!isset($current[$nestedKey])) {
                $current[$nestedKey] = [];
            }
            $current = &$current[$nestedKey];
        }
        $current = $value;
    }
}
