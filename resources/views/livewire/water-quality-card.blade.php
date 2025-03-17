<div>
    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="flex flex-col px-4 py-8 text-center rounded-lg bg-blue-50">
            <dt class="order-last text-lg font-medium text-gray-500">Temperature</dt>

            <dd class="text-4xl font-extrabold text-blue-600 md:text-5xl">{{ $this->temperature ?? '00.00' }}</dd>
        </div>

        <div class="flex flex-col px-4 py-8 text-center rounded-lg bg-blue-50">
            <dt class="order-last text-lg font-medium text-gray-500">Salinity</dt>

            <dd class="text-4xl font-extrabold text-blue-600 md:text-5xl">{{ $this->salinity ?? '00.00' }}</dd>
        </div>

        <div class="flex flex-col px-4 py-8 text-center rounded-lg bg-blue-50">
            <dt class="order-last text-lg font-medium text-gray-500">Dissolved Oxygen</dt>

            <dd class="text-4xl font-extrabold text-blue-600 md:text-5xl">{{ $this->dissolved_oxygen ?? '0.00' }}</dd>
        </div>

        <div class="flex flex-col px-4 py-8 text-center rounded-lg bg-blue-50">
            <dt class="order-last text-lg font-medium text-gray-500">pH</dt>

            <dd class="text-4xl font-extrabold text-blue-600 md:text-5xl">{{ $this->ph_level ?? '0.00'}}</dd>
        </div>
    </dl>
</div>
