@props(['max' => 5, 'value' => 0])

<div
    x-data="{
        rating: {{ $value }},
        hoverRating: 0,
        maxRating: {{ $max }},
        init() {
            this.$watch('rating', value => {
                this.$wire.set('{{ $attributes->wire('model')->value() }}', value);
            });
        }
    }"
    class="flex items-center"
>
    <template x-for="star in maxRating" :key="star">
        <button
            type="button"
            @click="rating = star"
            @mouseover="hoverRating = star"
            @mouseleave="hoverRating = 0"
            class="focus:outline-none"
        >
            <flux:icon
                name="star"
                variant="solid"
                class="h-6 w-6 text-yellow-400"
                x-show="hoverRating >= star || rating >= star"
            />
            <flux:icon
                name="star"
                variant="outline"
                class="h-6 w-6 text-gray-300 dark:text-gray-600"
                x-show="!(hoverRating >= star || rating >= star)"
            />
        </button>
    </template>
</div> 