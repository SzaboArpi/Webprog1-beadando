import React, { useState } from 'react';

const CounterHistory = ({ history }) => (
  <ul>
    {history.map((val, i) => (
      <li key={i}>Step {i + 1}: {val}</li>
    ))}
  </ul>
);

const CounterApp = () => {
  const [count, setCount] = useState(0);
  const [history, setHistory] = useState([]);

  const updateCount = (val) => {
    setCount(prev => {
      const newVal = prev + val;
      setHistory([...history, newVal]);
      return newVal;
    });
  };

  return (
    <div>
      <h2>Számláló</h2>
      <p>Érték: {count}</p>
      <button onClick={() => updateCount(1)}>+</button>
      <button onClick={() => updateCount(-1)}>-</button>
      <button onClick={() => {
        setCount(0);
        setHistory([]);
      }}>Reset</button>
      <h3>Előzmények</h3>
      <CounterHistory history={history} />
    </div>
  );
};

export default CounterApp;