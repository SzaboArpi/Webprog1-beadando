import React from 'react';
import CounterApp from './components/CounterApp';
import TodoApp from './components/TodoApp';

const App = () => {
  return (
    <div>
      <a href="http://webprog-beadando.nigusrt.nhely.hu/">vissza a főoldalra</a>
      <div style={{ padding: '2rem' }}>
        <h1>React App: Todo + Counter</h1>
        <CounterApp />
        <hr />
        <TodoApp />
      </div>
    </div>
  );
};


export default App;