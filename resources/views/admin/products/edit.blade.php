<x-admin-layout :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Productos', 'href' => route('admin.products.index')],
    ['name' => 'Editar'],
]" :title="'Producto'">
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
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
            <div class="mb-4">
                <label for="name"
                    class="block text-sm font-medium text-gray-700 dark:text-white text-left">Nombre</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('name') ? 'border-red-600 bg-red-500 text-red-400' : '' }}"
                    placeholder="Nombre del producto">
            </div>
            <div class="mb-4">
                <label for="description"
                    class="block text-sm font-medium text-gray-700 dark:text-white text-left">Descripción</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('description') ? 'border-red-500 bg-red-600' : '' }}">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="mb-4">
                <label for="price"
                    class="block text-sm font-medium text-gray-700 dark:text-white text-left">Precio</label>
                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50 {{ $errors->has('price') ? 'border-red-500 bg-red-600' : '' }}"
                    placeholder="Precio del producto">
            </div>
            <div class="mb-4">
                <label for="category_id"
                    class="block text-sm font-medium text-gray-700 dark:text-white text-left">Categoría</label>
                <select id="category_id" name="category_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                    <option value="">Seleccione una categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Actualizar
                    Producto</button>
            </div>
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
