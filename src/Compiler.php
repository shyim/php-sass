<?php

namespace ShyimSass;

use ShyimSass\Exception\CompileError;
use ShyimSass\Exception\FileNotFoundException;
use ShyimSass\Exception\InvalidOption;
use ShyimSass\Exception\UnsupportedPlatform;

class Compiler
{
    private const ALLOWED_OPTIONS = ['precision', 'output_style', 'source_comments', 'source_map_embed', 'source_map_contents', 'source_map_file_urls', 'omit_source_map_url', 'is_indented_syntax_src', 'indent', 'linefeed', 'include_path', 'source_map_file', 'source_map_root'];

    public const STYLE_NESTED = 0;
    public const STYLE_EXPANDED = 1;
    public const STYLE_COMPACT = 2;
    public const STYLE_COMPRESSED = 3;
    public const STYLE_INSPECT = 4;
    public const STYLE_TO_SASS = 5;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var \FFI
     */
    private $ffi;

    public function __construct()
    {
        $this->ffi = \FFI::cdef("
enum Sass_Output_Style {
    SASS_STYLE_NESTED,
    SASS_STYLE_EXPANDED,
    SASS_STYLE_COMPACT,
    SASS_STYLE_COMPRESSED,
    SASS_STYLE_INSPECT,
    SASS_STYLE_TO_SASS
};
enum Sass_Compiler_State {
    SASS_COMPILER_CREATED,
    SASS_COMPILER_PARSED,
    SASS_COMPILER_EXECUTED
};
struct Sass_Compiler;
struct Sass_Options;
struct Sass_Context;
struct Sass_File_Context;
struct Sass_Data_Context;
struct Sass_Import;
struct Sass_Options;
struct Sass_Compiler;
struct Sass_Importer;
struct Sass_Function;
typedef struct Sass_Import(*Sass_Import_Entry);
typedef struct Sass_Import*(*Sass_Import_List);
typedef struct Sass_Importer(*Sass_Importer_Entry);
typedef struct Sass_Importer*(*Sass_Importer_List);
typedef Sass_Import_List(*Sass_Importer_Fn)(const char* url, Sass_Importer_Entry cb, struct Sass_Compiler* compiler);
typedef struct Sass_Function(*Sass_Function_Entry);
typedef struct Sass_Function*(*Sass_Function_List);
typedef union Sass_Value*(*Sass_Function_Fn)(const union Sass_Value*, Sass_Function_Entry cb, struct Sass_Compiler* compiler);
void* sass_alloc_memory(size_t size);
void  sass_free_memory(void* ptr);
char* sass_copy_c_string(const char* str);
char* sass_string_quote(const char* str, const char quote_mark);
char* sass_string_unquote(const char* str);
const char* libsass_version(void);
const char* libsass_language_version(void);
struct Sass_Options* sass_make_options(void);
struct Sass_File_Context* sass_make_file_context(const char* input_path);
struct Sass_Data_Context* sass_make_data_context(char* source_string);
int sass_compile_file_context(struct Sass_File_Context* ctx);
int sass_compile_data_context(struct Sass_Data_Context* ctx);
struct Sass_Compiler* sass_make_file_compiler(struct Sass_File_Context* file_ctx);
struct Sass_Compiler* sass_make_data_compiler(struct Sass_Data_Context* data_ctx);
int sass_compiler_parse(struct Sass_Compiler* compiler);
int sass_compiler_execute(struct Sass_Compiler* compiler);
void sass_delete_compiler(struct Sass_Compiler* compiler);
void sass_delete_options(struct Sass_Options* options);
void sass_delete_file_context(struct Sass_File_Context* ctx);
void sass_delete_data_context(struct Sass_Data_Context* ctx);
struct Sass_Context* sass_file_context_get_context(struct Sass_File_Context* file_ctx);
struct Sass_Context* sass_data_context_get_context(struct Sass_Data_Context* data_ctx);
struct Sass_Options* sass_context_get_options(struct Sass_Context* ctx);
struct Sass_Options* sass_file_context_get_options(struct Sass_File_Context* file_ctx);
struct Sass_Options* sass_data_context_get_options(struct Sass_Data_Context* data_ctx);
void sass_file_context_set_options(struct Sass_File_Context* file_ctx, struct Sass_Options* opt);
void sass_data_context_set_options(struct Sass_Data_Context* data_ctx, struct Sass_Options* opt);
int sass_option_get_precision(struct Sass_Options* options);
enum Sass_Output_Style sass_option_get_output_style(struct Sass_Options* options);
bool sass_option_get_source_comments(struct Sass_Options* options);
bool sass_option_get_source_map_embed(struct Sass_Options* options);
bool sass_option_get_source_map_contents(struct Sass_Options* options);
bool sass_option_get_source_map_file_urls(struct Sass_Options* options);
bool sass_option_get_omit_source_map_url(struct Sass_Options* options);
bool sass_option_get_is_indented_syntax_src(struct Sass_Options* options);
const char* sass_option_get_indent(struct Sass_Options* options);
const char* sass_option_get_linefeed(struct Sass_Options* options);
const char* sass_option_get_input_path(struct Sass_Options* options);
const char* sass_option_get_output_path(struct Sass_Options* options);
const char* sass_option_get_include_path(struct Sass_Options* options);
const char* sass_option_get_source_map_file(struct Sass_Options* options);
const char* sass_option_get_source_map_root(struct Sass_Options* options);
Sass_Importer_List sass_option_get_c_headers(struct Sass_Options* options);
Sass_Importer_List sass_option_get_c_importers(struct Sass_Options* options);
Sass_Function_List sass_option_get_c_functions(struct Sass_Options* options);
void sass_option_set_precision(struct Sass_Options* options, int precision);
void sass_option_set_output_style(struct Sass_Options* options, enum Sass_Output_Style output_style);
void sass_option_set_source_comments(struct Sass_Options* options, bool source_comments);
void sass_option_set_source_map_embed(struct Sass_Options* options, bool source_map_embed);
void sass_option_set_source_map_contents(struct Sass_Options* options, bool source_map_contents);
void sass_option_set_source_map_file_urls(struct Sass_Options* options, bool source_map_file_urls);
void sass_option_set_omit_source_map_url(struct Sass_Options* options, bool omit_source_map_url);
void sass_option_set_is_indented_syntax_src(struct Sass_Options* options, bool is_indented_syntax_src);
void sass_option_set_indent(struct Sass_Options* options, const char* indent);
void sass_option_set_linefeed(struct Sass_Options* options, const char* linefeed);
void sass_option_set_input_path(struct Sass_Options* options, const char* input_path);
void sass_option_set_output_path(struct Sass_Options* options, const char* output_path);
void sass_option_set_plugin_path(struct Sass_Options* options, const char* plugin_path);
void sass_option_set_include_path(struct Sass_Options* options, const char* include_path);
void sass_option_set_source_map_file(struct Sass_Options* options, const char* source_map_file);
void sass_option_set_source_map_root(struct Sass_Options* options, const char* source_map_root);
void sass_option_set_c_headers(struct Sass_Options* options, Sass_Importer_List c_headers);
void sass_option_set_c_importers(struct Sass_Options* options, Sass_Importer_List c_importers);
void sass_option_set_c_functions(struct Sass_Options* options, Sass_Function_List c_functions);
const char* sass_context_get_output_string(struct Sass_Context* ctx);
int sass_context_get_error_status(struct Sass_Context* ctx);
const char* sass_context_get_error_json(struct Sass_Context* ctx);
const char* sass_context_get_error_text(struct Sass_Context* ctx);
const char* sass_context_get_error_message(struct Sass_Context* ctx);
const char* sass_context_get_error_file(struct Sass_Context* ctx);
const char* sass_context_get_error_src(struct Sass_Context* ctx);
size_t sass_context_get_error_line(struct Sass_Context* ctx);
size_t sass_context_get_error_column(struct Sass_Context* ctx);
const char* sass_context_get_source_map_string(struct Sass_Context* ctx);
char** sass_context_get_included_files(struct Sass_Context* ctx);
size_t sass_context_get_included_files_size(struct Sass_Context* ctx);
char* sass_context_take_error_json(struct Sass_Context* ctx);
char* sass_context_take_error_text(struct Sass_Context* ctx);
char* sass_context_take_error_message(struct Sass_Context* ctx);
char* sass_context_take_error_file(struct Sass_Context* ctx);
char* sass_context_take_output_string(struct Sass_Context* ctx);
char* sass_context_take_source_map_string(struct Sass_Context* ctx);
char** sass_context_take_included_files(struct Sass_Context* ctx);
enum Sass_Compiler_State sass_compiler_get_state(struct Sass_Compiler* compiler);
struct Sass_Context* sass_compiler_get_context(struct Sass_Compiler* compiler);
struct Sass_Options* sass_compiler_get_options(struct Sass_Compiler* compiler);
size_t sass_compiler_get_import_stack_size(struct Sass_Compiler* compiler);
Sass_Import_Entry sass_compiler_get_last_import(struct Sass_Compiler* compiler);
Sass_Import_Entry sass_compiler_get_import_entry(struct Sass_Compiler* compiler, size_t idx);
void sass_option_push_plugin_path(struct Sass_Options* options, const char* path);
void sass_option_push_include_path(struct Sass_Options* options, const char* path);
char* sass2scss(const char* sass, const int options);
const char* sass2scss_version(void);
void* malloc(size_t size);
", $this->getBinaryPath());
    }

    public function setOptions(array $options)
    {
        foreach ($options as $k => $v) {
            if (!in_array($k, self::ALLOWED_OPTIONS)) {
                throw new InvalidOption(sprintf('Option %s is not allowed', $k));
            }
        }

        $this->options = $options;
    }

    public function compile(string $filePath, string $destPath = null): ?string
    {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException(sprintf('Cannot find file %s', $filePath));
        }

        $file = $this->ffi->sass_make_file_context($filePath);
        $context = $this->ffi->sass_file_context_get_context($file);

        $this->applyOptions($file, $filePath, $destPath);
        $status = $this->ffi->sass_compile_file_context($file);

        if ($status !== 0) {
            throw new CompileError(sprintf('%s in file: %s', $this->ffi->sass_context_get_error_message($context), $this->ffi->sass_context_get_error_file($context)));
        }

        $output = $this->ffi->sass_context_get_output_string($context);

        if ($destPath === null) {
            return $output;
        }

        $this->ffi->sass_delete_file_context($file);

        file_put_contents($destPath, $output);

        return null;
    }

    public function getVersion(): string
    {
        return $this->ffi->libsass_language_version();
    }

    public function callNative(string $name, ...$args)
    {
        return call_user_func_array([$this->ffi, $name], $args);
    }

    private function applyOptions($context, string $filePath, string $destPath = null)
    {
        $options = $this->ffi->sass_make_options();

        if ($destPath) {
            $this->ffi->sass_option_set_output_path($options, $destPath);
        }

        foreach ($this->options as $name => $value) {
            call_user_func_array([$this->ffi, 'sass_option_set_' . $name], [$options, $value]);
        }

        $this->ffi->sass_option_set_input_path($options, $filePath);

        $this->ffi->sass_file_context_set_options($context, $options);
    }

    private function getBinaryPath(): string
    {
        if (PHP_OS !== 'Linux') {
            throw new UnsupportedPlatform(sprintf('Platform %s is currently not supported', PHP_OS));
        }

        exec('getconf GNU_LIBC_VERSION', $output, $retVar);
        if ($retVar === 0) {
            return dirname(__DIR__) . '/binaries/linux/gnu.so';
        }

        return dirname(__DIR__) . '/binaries/linux/musl.so';
    }
}