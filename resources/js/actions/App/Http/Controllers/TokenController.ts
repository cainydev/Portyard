import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TokenController::entry
* @see app/Http/Controllers/TokenController.php:21
* @route '/auth/token'
*/
export const entry = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: entry.url(options),
    method: 'get',
})

entry.definition = {
    methods: ["get","head"],
    url: '/auth/token',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TokenController::entry
* @see app/Http/Controllers/TokenController.php:21
* @route '/auth/token'
*/
entry.url = (options?: RouteQueryOptions) => {
    return entry.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TokenController::entry
* @see app/Http/Controllers/TokenController.php:21
* @route '/auth/token'
*/
entry.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: entry.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TokenController::entry
* @see app/Http/Controllers/TokenController.php:21
* @route '/auth/token'
*/
entry.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: entry.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TokenController::entry
* @see app/Http/Controllers/TokenController.php:21
* @route '/auth/token'
*/
const entryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: entry.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TokenController::entry
* @see app/Http/Controllers/TokenController.php:21
* @route '/auth/token'
*/
entryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: entry.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TokenController::entry
* @see app/Http/Controllers/TokenController.php:21
* @route '/auth/token'
*/
entryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: entry.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

entry.form = entryForm

const TokenController = { entry }

export default TokenController