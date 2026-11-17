<?php
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Omatech\Lars\BaseRepository;
use Omatech\Lars\Contracts\CriteriaInterface;
use Orchestra\Testbench\TestCase;

class FixtureInventory extends Model
{
    protected $table = 'fixture_inventory';
    protected $guarded = [];
    public $timestamps = false;
}
class FixtureRepository extends BaseRepository { public function model(): string { return FixtureInventory::class; } }
class ActiveCriteria implements CriteriaInterface { public function apply(Builder $q): Builder { return $q->where('active', 1); } }
class OrderedCriteria implements CriteriaInterface { public function apply(Builder $q): Builder { return $q->orderBy('name'); } }
final class RepositoryTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('fixture_inventory', function (Blueprint $table): void { $table->id(); $table->string('name'); $table->boolean('active'); });
        FixtureInventory::insert([['name' => 'B', 'active' => 1], ['name' => 'A', 'active' => 0], ['name' => 'C', 'active' => 1]]);
    }
    public function test_criteria_filter_order_remove_and_reset_a_fresh_query(): void
    {
        $repo = new FixtureRepository();
        $repo->pushCriteria(new ActiveCriteria())->pushCriteria(new OrderedCriteria());
        self::assertSame(['B', 'C'], $repo->query()->pluck('name')->all());
        $repo->popCriteria(new ActiveCriteria());
        self::assertSame(['A', 'B', 'C'], $repo->query()->pluck('name')->all());
        $repo->resetCriteria();
        self::assertCount(0, $repo->getCriterias());
        self::assertSame(3, $repo->query()->count());
    }
    public function test_a_non_model_is_rejected_before_a_query_can_run(): void
    {
        $repo = new class extends BaseRepository { public function model(): string { return stdClass::class; } };
        $this->expectException(RuntimeException::class);
        $repo->query();
    }
}
