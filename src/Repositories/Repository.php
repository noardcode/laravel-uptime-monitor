<?php

namespace Noardcode\LaravelUptimeMonitor\Repositories;

use Closure;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class Repository
 * @package App\Repositories
 */
abstract class Repository
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @var string
     */
    protected string $orderBy = 'id';

    /**
     * @var bool
     */
    protected bool $orderByAscending = true;

    /**
     * @var string
     */
    protected $saveErrorMsg;

    /**
     * @var string
     */
    protected $deleteErrorMsg;

    /**
     * Repository constructor.
     *
     * @param Model $model
     */
    protected function __construct(Model $model)
    {
        $this->model = $model;

        $this->saveErrorMsg = __('Item has been saved.');
        $this->deleteErrorMsg = __('Item could not be saved.');
    }

    /**
     * @param int|null $limit
     * @param Builder|null $builder
     *
     * @return LengthAwarePaginator|Builder[]|Collection
     */
    public function get(int $limit = null, Builder $builder = null)
    {
        $builder = $builder ?? $this->getBuilder();

        if (!is_null($limit) && is_numeric($limit)) {
            return $builder->paginate($limit);
        }

        return $builder->get();
    }

    /**
     * @param Request $request
     * @return Model|null
     * @throws Exception
     */
    public function create(Request $request): ?Model
    {
        return $this->save($request->validated());
    }

    /**
     * @param Request $request
     * @param Model|null $model
     * @return Model|null
     * @throws Exception
     */
    public function update(Request $request, Model $model): ?Model
    {
        $data = $request->validated();
        return $this->save($data, $model);
    }

    /**
     * @param array $data
     * @param Model|null $model
     *
     * @param Closure|null $callback
     * @return Model
     * @throws Exception
     */
    final public function save(array $data, Model $model = null, Closure $callback = null): ?Model
    {
        DB::beginTransaction();

        try {
            $this->model = $model ?? $this->model->newInstance();
            $this->model->fill($this->getFillableData($data));
            $this->model->save();

            if (!is_null($callback)) {
                $callback($this->model);
            }

            DB::commit();
            session()->remove('form-sessions.' . session('currentViewRoute'));
            return $this->model;
        } catch (Exception $e) {
            DB::rollBack();
            if (!request()->ajax()) {
                throw $e;
            }
            return null;
        }
    }

    /**
     * @param Model $model
     *
     * @param Closure|null $closure
     * @throws Exception
     */
    public function delete(Model $model, Closure $closure = null, bool $forceDelete = false): void
    {
        DB::beginTransaction();

        try {
            if (!is_null($closure)) {
                $closure($model);
            }

            if ($forceDelete) {
                $model->forceDelete();
            } else {
                $model->delete();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            session()->flash('warning', $this->deleteErrorMsg);

            throw $e;
        }
    }

    /**
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->model->orderBy($this->orderBy, $this->orderByAscending ? 'ASC' : 'DESC');
    }

    /**
     * @param string $collection
     */
    public function setCollection(string $collection): self
    {
        $this->model->setCollection($collection);
        return $this;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getFillableData(array $data): array
    {
        return !empty($this->model->getFillable()) ? Arr::only($data, $this->model->getFillable()) : $data;
    }
}
