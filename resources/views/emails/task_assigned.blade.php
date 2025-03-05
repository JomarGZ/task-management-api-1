<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Task Assigned Notification</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
  <body class="bg-gray-100 font-sans">
    <!-- Email Container -->
    <div class="mx-auto my-10 max-w-2xl overflow-hidden rounded-lg bg-white shadow-lg">
      <!-- Header -->
      <div class="bg-blue-600 p-6 text-center">
        <!-- Logo and Website Name -->
        <div class="flex items-center justify-center space-x-3">
          <img src="https://i.pravatar.cc/40" alt="SprintSync Logo" class="h-10 w-10 rounded-full" />
          <h1 class="text-2xl font-bold text-white">SprintSync</h1>
        </div>
        <p class="mt-2 text-sm text-white">You have been assigned a new task.</p>
      </div>

      <!-- Body -->
      <div class="p-6">
        <!-- Assigned By -->
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800">Assigned By</h2>
          <p class="mt-2 text-gray-600">The task has been assigned to you by:</p>
          <div class="mt-4 rounded-lg bg-gray-50 p-4">
            {{-- <p class="font-medium text-gray-800">Jane Doe</p> --}}
            {{-- <p class="mt-1 text-sm text-gray-600">Project Manager</p> --}}
          </div>
        </div>

        <!-- Task Details -->
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800">Task Details</h2>
          <p class="mt-2 text-gray-600">Here are the details of the task:</p>
          <div class="mt-4 rounded-lg bg-gray-50 p-4">
            <p class="font-medium text-gray-800">{{ $task->title ?? '' }}</p>
            <p class="mt-1 text-sm text-gray-600">Priority: {{ $task->priority_level ?? '--' }}</p>
          </div>
        </div>
        <!-- Project Details -->
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-800">Project Details</h2>
          <p class="mt-2 text-gray-600">This task is part of the following project:</p>
          <div class="mt-4 rounded-lg bg-gray-50 p-4">
            <p class="font-medium text-gray-800">{{ $project->name ?? '' }}</p>
            {{-- <p class="mt-1 text-sm text-gray-600">Project Manager: John Doe</p> --}}
          </div>
        </div>
        <!-- Call-to-Action Button -->
        <div class="text-center">
          <a href="{{ $site_url ?? '#' }}" class="inline-block rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white transition duration-300 hover:bg-blue-700"> View Task on SprintSync </a>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50 p-6 text-center">
        <p class="text-sm text-gray-600">Thank you for using <span class="font-semibold">SprintSync</span>!</p>
        <p class="mt-2 text-xs text-gray-500">If you have any questions, feel free to contact us at <a href="mailto:support@sprintsync.tech" class="text-blue-600 hover:underline">support@sprintsync.tech</a>.</p>
      </div>
    </div>
  </body>
</html>
