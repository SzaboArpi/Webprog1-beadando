import React, { useState } from 'react';

class Task {
  constructor(id, text) {
    this.id = id;
    this.text = text;
    this.done = false;
  }

  toggle() {
    this.done = !this.done;
  }
}

const TodoItem = ({ task, onToggle, onDelete }) => (
  <li style={{ textDecoration: task.done ? 'line-through' : 'none' }}>
    {task.text}
    <button onClick={() => onToggle(task.id)}>✔</button>
    <button onClick={() => onDelete(task.id)}>🗑</button>
  </li>
);

const TodoApp = () => {
  const [tasks, setTasks] = useState([]);
  const [text, setText] = useState('');

  const addTask = () => {
    if (!text.trim()) return;
    const newTask = new Task(Date.now(), text.trim());
    setTasks(prev => [...prev, newTask]);
    setText('');
  };

  const toggleTask = (id) => {
    setTasks(prev => prev.map(t => {
      if (t.id === id) {
        const task = new Task(t.id, t.text);
        task.done = !t.done;
        return task;
      }
      return t;
    }));
  };

  const deleteTask = (id) => {
    setTasks(prev => prev.filter(t => t.id !== id));
  };

  return (
    <div>
      <h2>Teendők</h2>
      <input
        value={text}
        onChange={e => setText(e.target.value)}
        placeholder="Új feladat"
      />
      <button onClick={addTask}>Hozzáadás</button>
      <ul>
        {tasks.map(task => (
          <TodoItem
            key={task.id}
            task={task}
            onToggle={toggleTask}
            onDelete={deleteTask}
          />
        ))}
      </ul>
    </div>
  );
};

export default TodoApp;