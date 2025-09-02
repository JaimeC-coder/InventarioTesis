<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Productos', 'href' => route('admin.products.index')],
    ['name' => 'Editar'],
]" :title="'Producto'">
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
        <style>
            .image-product {
                width: 5rem;
                height: 2.5rem;
                object-fit: cover;
                object-position: center
            }
        </style>
    @endpush
    <div
        class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">

        <div class="mb-4">
            <form action="{{ route('admin.products.uploadImages', $product) }}" class="dropzone" id="my-dropzone"
                method="POST"> @csrf</form>
        </div>
        <form action="{{ route('admin.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            <x-forms.input label="Nombre" name="name" type="text" value="{{ old('name', $product->name) }}" />
            <x-forms.textarea label="Descripción" name="description">
                {{ old('description', $product->description) }}</x-forms.textarea>
            <x-forms.input label="Precio" name="price" type="number" value="{{ old('price', $product->price) }}" />

            <x-forms.select label="Categoría" name="category_uuid" :options="$categories" option-label="name"
                option-value="uuid" />
            <x-button type="submit" class="mt-4">
                Actualizar Producto
            </x-button>
        </form>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

        <script>
            Dropzone.options.myDropzone = {
                addRemoveLinks: true,
                init: function() {
                    let myDropzone = this;

                    let images = @json($product->images);
                    console.log(images);

                    images.forEach(function(image) {
                        let mockFile = {
                            id: image.uuid,
                            name: image.alt_text,
                            size: image.size
                        };

                        // Agregar el archivo
                        myDropzone.emit("addedfile", mockFile);

                        // Usar la URL completa para el thumbnail
                        let imageUrl = "{{ asset('storage/') }}/" + image.path;
                        myDropzone.emit("thumbnail", mockFile, imageUrl);

                        // Marcar como completado
                        myDropzone.emit("complete", mockFile);
                        // Agregar el botón de eliminar
                        myDropzone.files.push(mockFile);
                    });

                    this.on("success", function(file, response) {
                        // Aquí puedes manejar la respuesta del servidor
                        file.id = response.uuid; // Asignar el UUID de la imagen al archivo
                        console.log("Imagen subida con éxito:", response);
                    });

                    this.on("removedfile", function(file) {
                        // Eliminar la imagen del servidor
                        let imageId = file.id;
                        console.log("Eliminando imagen con ID:", imageId);
                        fetch(`/admin/images/${imageId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(response => {
                            if (response.ok) {
                                console.log("Imagen eliminada");
                            } else {
                                console.error("Error al eliminar la imagen");
                            }
                        });
                    });
                }
            };
        </script>
    @endpush
</x-admin-layout>
