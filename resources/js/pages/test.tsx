import StoreLayout from '@/layouts/StoreLayout';
import { useRef } from 'react';
import { create } from 'zustand';

interface Todo {
    id: string;
    text: string;
    completed: boolean;
}
interface TodoStore {
    todos: Todo[];
    addTodo: (text: string) => void;
    toggleTodo: (id: string) => void;
    removeTodo: (id: string) => void;
    filter: 'all' | 'uncompleted' | 'completed';
    setFilter: (filter: 'all' | 'uncompleted' | 'completed') => void;
}
const useTodoStore = create<TodoStore>((set, get) => ({
    todos: [],
    filter: 'all',

    addTodo: (text) => {
        set((state) => ({
            todos: [
                ...state.todos,
                {
                    id: Date.now().toString(),
                    text,
                    completed: false,
                },
            ],
        }));
        get().setFilter('all');
    },

    toggleTodo: (id) =>
        set((state) => ({
            todos: state.todos.map((todo) =>
                todo.id === id ? { ...todo, completed: !todo.completed } : todo,
            ),
        })),

    removeTodo: (id) =>
        set((state) => ({
            todos: state.todos.filter((todo) => todo.id !== id),
        })),

    setFilter: (filter) => set({ filter }),
}));

/* =====================
   Todo Header
===================== */
function TodoHeader() {
    return (
        <div className="mb-6 text-center">
            <h1 className="text-3xl font-bold text-gray-800">Todo List</h1>
            <p className="mt-2 text-gray-500">Manage your daily tasks</p>
        </div>
    );
}

/* =====================
   Todo Input
===================== */
function TodoInput() {
    const inputRef = useRef<HTMLInputElement>(null);
    const { addTodo } = useTodoStore();
    return (
        <div className="mb-6 flex gap-3">
            <input
                type="text"
                ref={inputRef}
                placeholder="Add new task..."
                className="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-black focus:ring-2 focus:ring-blue-500 focus:outline-none"
            />
            <button
                onClick={() => {
                    inputRef.current?.value && addTodo(inputRef.current?.value);
                }}
                className="rounded-lg bg-blue-600 px-6 py-2 text-white transition hover:bg-blue-700"
            >
                Add
            </button>
        </div>
    );
}

/* =====================
   Todo Filters (UI ONLY)
===================== */
function TodoFilters() {
    return (
        <div className="mb-6 flex justify-center gap-2">
            <FilterButton filterName="all"></FilterButton>
            <FilterButton filterName="uncompleted"></FilterButton>
            <FilterButton filterName="completed"></FilterButton>
        </div>
    );
}

function FilterButton({
    filterName,
}: {
    filterName: 'all' | 'uncompleted' | 'completed';
}) {
    const { filter, setFilter } = useTodoStore();
    return (
        <button
            onClick={() => setFilter(filterName)}
            className={`rounded-lg px-4 py-2 text-sm font-medium transition ${
                filter == filterName.toLowerCase()
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
            }`}
        >
            {filterName.toUpperCase()}
        </button>
    );
}

/* =====================
   Todo Item
===================== */
function TodoItem({ todo }: { todo: Todo }) {
    const { removeTodo, toggleTodo } = useTodoStore();
    return (
        <div className="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3">
            <div className="flex items-center gap-3">
                <input
                    type="checkbox"
                    checked={todo.completed}
                    onChange={() => toggleTodo(todo.id)}
                    className="h-4 w-4"
                />
                <span className="text-gray-700">Sample todo item</span>
            </div>

            <button
                onClick={() => removeTodo(todo.id)}
                className="text-red-500 transition hover:text-red-700"
            >
                Delete
            </button>
        </div>
    );
}

/* =====================
   Todo List
===================== */
function TodoList() {
    const { todos, filter } = useTodoStore();

    const filteredTodos = todos.filter((todo) => {
        if (filter === 'all') return true;
        if (filter === 'completed') return todo.completed;
        return !todo.completed;
    });

    return (
        <div className="space-y-3">
            {filteredTodos.map((todo) => (
                <TodoItem
                    key={todo.id}
                    todo={todo}
                />
            ))}
        </div>
    );
}

/* =====================
   Page
===================== */
export default function ProductDetails() {
    return (
        <StoreLayout>
            <section className="my-10">
                <div className="mx-auto max-w-xl rounded-xl bg-white p-6 shadow">
                    <TodoHeader />
                    <TodoInput />
                    <TodoFilters />
                    <TodoList />
                </div>
            </section>
        </StoreLayout>
    );
}
